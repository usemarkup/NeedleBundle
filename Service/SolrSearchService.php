<?php

namespace Markup\NeedleBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use GuzzleHttp\Promise\PromiseInterface;
use Markup\NeedleBundle\Adapter\GroupedResultAdapter;
use Markup\NeedleBundle\Adapter\SolariumResultPromisePagerfantaAdapter;
use Markup\NeedleBundle\Builder\SolariumSelectQueryBuilder;
use Markup\NeedleBundle\Context\SearchContextInterface;
use Markup\NeedleBundle\Query\ResolvedSelectQuery;
use Markup\NeedleBundle\Query\ResolvedSelectQueryDecoratorInterface;
use Markup\NeedleBundle\Query\SelectQueryInterface;
use Markup\NeedleBundle\Result\PagerfantaResultAdapter;
use Markup\NeedleBundle\Result\SolariumDebugOutputStrategy;
use Markup\NeedleBundle\Result\SolariumFacetSetsStrategy;
use Markup\NeedleBundle\Result\SolariumSpellcheckResultStrategy;
use Pagerfanta\Pagerfanta;
use Shieldo\SolariumAsyncPlugin;
use Solarium\Client as SolariumClient;
use Symfony\Component\Templating\EngineInterface as TemplatingEngine;
use function GuzzleHttp\Promise\coroutine;
use function GuzzleHttp\Promise\promise_for;

/**
 * A search service using Solr/ Solarium.
 *
 * Composes a SelectQuery & SearchContext (into a ResolvedQuery), converts it into a solr query and
 * executes the query (using Solarium) and then returns a result (as a PagerfantaResultAdapter)
 */
class SolrSearchService implements AsyncSearchServiceInterface
{
    /**
     * Solr needs specification of a very large number for a 'view all' function.
     * An infinitely large number, in fact.  Like 600.
     **/
    const INFINITY = 600;

    /**
     * A Solarium client.
     *
     * @var SolariumClient
     **/
    private $solarium;

    /**
     * A builder object that can form Solarium queries from generic ones.
     *
     * @var SolariumSelectQueryBuilder
     **/
    private $solariumQueryBuilder;

    /**
     * @var TemplatingEngine|null
     **/
    private $templating = null;

    /**
     * A context for searches.
     *
     * @var SearchContextInterface|null
     **/
    private $context = null;

    /**
     * @var ArrayCollection
     **/
    private $decorators = null;

    public function __construct(
        SolariumClient $solarium,
        SolariumSelectQueryBuilder $solariumQueryBuilder,
        ?TemplatingEngine $templating = null
    ) {
        $this->solarium = $solarium;
        $this->solariumQueryBuilder = $solariumQueryBuilder;
        $this->templating = $templating;
        $this->decorators = new ArrayCollection();
    }

    /**
     * @param  SelectQueryInterface    $query
     * @return PagerfantaResultAdapter
     */
    public function executeQuery(SelectQueryInterface $query)
    {
        return $this->executeQueryAsync($query)->wait();
    }

    /**
     * Provides a promise for a executing a select query on a service, returning a result.
     *
     * @param SelectQueryInterface $query
     * @return PromiseInterface
     **/
    public function executeQueryAsync(SelectQueryInterface $query)
    {
        $createSolariumQuery = function () {
            return $this->solarium->createSelect();
        };

        return coroutine(
            function () use ($query, $createSolariumQuery) {
                $solariumQueryBuilder = $this->getSolariumQueryBuilder();

                $query = new ResolvedSelectQuery(
                    $query,
                    ($this->getContext() instanceof SearchContextInterface) ? $this->getContext() : null
                );

                foreach ($this->decorators as $decorator) {
                    $query = $decorator->decorate($query);
                }
                $solariumQuery = $solariumQueryBuilder->buildSolariumQueryFromGeneric($query, $createSolariumQuery);

                //apply offset/limit
                $maxPerPage = $query->getMaxPerPage();
                if (null === $maxPerPage && !is_null($this->getContext()) && $query->getPageNumber() !== null) {
                    $maxPerPage = $this->getContext()->getItemsPerPage() ?: null;
                }
                $solariumQuery->setRows($maxPerPage ?: self::INFINITY);


                $page = $query->getPageNumber();
                if ($page && $maxPerPage) {
                    $solariumQuery->setStart($maxPerPage * ($page-1));
                }

                $pluginIndex = 'async';
                /** @var SolariumAsyncPlugin $asyncPlugin */
                $asyncPlugin = $this->solarium
                    ->registerPlugin($pluginIndex, new SolariumAsyncPlugin())
                    ->getPlugin($pluginIndex);

                $solariumResult = $this->solarium->createResult(
                    $solariumQuery,
                    (yield promise_for($asyncPlugin->queryAsync($solariumQuery)))
                );
                if ($query->getGroupingField()) {
                    $solariumResult = new GroupedResultAdapter($solariumResult);
                }

                $pagerfanta = new Pagerfanta(new SolariumResultPromisePagerfantaAdapter(promise_for($solariumResult)));
                $pagerfanta->setMaxPerPage($maxPerPage ?: self::INFINITY);
                $pagerfanta->setCurrentPage($page ?: 1);

                $result = new PagerfantaResultAdapter($pagerfanta);

                $resultClosure = function () use ($solariumResult) {
                    return $solariumResult;
                };

                //set the strategy to fetch facet sets, as these are not handled by pagerfanta
                if (!is_null($this->getContext())) {
                    $result->setFacetSetStrategy(
                        new SolariumFacetSetsStrategy($resultClosure, $this->getContext(), $query->getRecord())
                    );
                }

                //set any spellcheck result
                $result->setSpellcheckResultStrategy(new SolariumSpellcheckResultStrategy($resultClosure, $query));

                //set strategy for debug information output as this is not available through pagerfanta - only if templating service was available
                if (null !== $this->templating) {
                    $result->setDebugOutputStrategy(new SolariumDebugOutputStrategy($resultClosure, $this->templating));
                }

                yield $result;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setContext(SearchContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function addDecorator(ResolvedSelectQueryDecoratorInterface $decorator)
    {
        $this->decorators->add($decorator);

        return $this;
    }

    /**
     * Gets the context for the search.  Returns null if none set.
     **/
    private function getContext(): ?SearchContextInterface
    {
        return $this->context;
    }

    /**
     * @return SolariumSelectQueryBuilder
     **/
    private function getSolariumQueryBuilder()
    {
        return $this->solariumQueryBuilder;
    }
}
