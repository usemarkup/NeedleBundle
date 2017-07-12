<?php

namespace Markup\NeedleBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use function GuzzleHttp\Promise\coroutine;
use function GuzzleHttp\Promise\promise_for;
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
     * @var TemplatingEngine
     **/
    private $templating = null;

    /**
     * A context for searches.
     *
     * @var SearchContextInterface
     **/
    private $context = null;

    /**
     * @var ArrayCollection
     **/
    private $decorators = null;

    /**
     * @param SolariumClient             $solarium
     * @param SolariumSelectQueryBuilder $solariumQueryBuilder
     * @param TemplatingEngine|null      $templating
     **/
    public function __construct(
        SolariumClient $solarium,
        SolariumSelectQueryBuilder $solariumQueryBuilder,
        TemplatingEngine $templating = null
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
     * @param SelectQueryInterface
     * @return PromiseInterface
     **/
    public function executeQueryAsync(SelectQueryInterface $query)
    {
        return coroutine(
            function () use ($query) {
                $solariumQueryBuilder = $this->getSolariumQueryBuilder();

                $query = new ResolvedSelectQuery($query, $this->hasContext() ? $this->getContext() : null);

                foreach ($this->decorators as $decorator) {
                    $query = $decorator->decorate($query);
                }
                $solariumQuery = $solariumQueryBuilder->buildSolariumQueryFromGeneric($query);

                //apply offset/limit
                $maxPerPage = $query->getMaxPerPage();
                if (null === $maxPerPage && $this->hasContext() && $query->getPageNumber() !== null) {
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
                $pagerfanta->setCurrentPage($page ?: 1);
                $pagerfanta->setMaxPerPage($maxPerPage ?: self::INFINITY);

                $result = new PagerfantaResultAdapter($pagerfanta);

                $resultClosure = function () use ($solariumResult) {
                    return $solariumResult;
                };

                //set the strategy to fetch facet sets, as these are not handled by pagerfanta
                if ($this->hasContext()) {
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
     * @return bool
     **/
    private function hasContext()
    {
        return !is_null($this->context);
    }

    /**
     * Gets the context for the search.  Returns false if none set.
     *
     * @return SearchContextInterface|bool
     **/
    private function getContext()
    {
        if (null === $this->context) {
            return false;
        }

        return $this->context;
    }

    /**
     * @return SolariumClient
     **/
    private function getSolariumClient()
    {
        return $this->solarium;
    }

    /**
     * @return SolariumSelectQueryBuilder
     **/
    private function getSolariumQueryBuilder()
    {
        return $this->solariumQueryBuilder;
    }
}
