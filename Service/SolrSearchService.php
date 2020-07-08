<?php

namespace Markup\NeedleBundle\Service;

use Markup\NeedleBundle\Adapter\GroupedResultAdapter;
use Markup\NeedleBundle\Adapter\SolariumResultPromisePagerfantaAdapter;
use Markup\NeedleBundle\Builder\SolariumSelectQueryBuilder;
use Markup\NeedleBundle\Context\NoopSearchContext;
use Markup\NeedleBundle\Context\SearchContextInterface;
use Markup\NeedleBundle\Facet\AggregateFacetValueCanonicalizer;
use Markup\NeedleBundle\Facet\FacetValueCanonicalizer;
use Markup\NeedleBundle\Facet\FacetValueCanonicalizerInterface;
use Markup\NeedleBundle\Query\ResolvedSelectQuery;
use Markup\NeedleBundle\Query\ResolvedSelectQueryInterface;
use Markup\NeedleBundle\Result\PagerfantaResultAdapter;
use Markup\NeedleBundle\Result\ResultInterface;
use Markup\NeedleBundle\Result\SolariumDebugOutputStrategy;
use Markup\NeedleBundle\Result\SolariumFacetSetsStrategy;
use Markup\NeedleBundle\Result\SolariumSpellcheckResultStrategy;
use Pagerfanta\Pagerfanta;
use Shieldo\SolariumAsyncPlugin;
use Solarium\Client as SolariumClient;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
     * @var string
     */
    private $corpus;

    /**
     * @var TemplatingEngine|null
     **/
    private $templating = null;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var FacetValueCanonicalizerInterface
     */
    private $facetValueCanonicalizer;

    public function __construct(
        SolariumClient $solarium,
        SolariumSelectQueryBuilder $solariumQueryBuilder,
        EventDispatcherInterface $eventDispatcher,
        FacetValueCanonicalizerInterface $facetValueCanonicalizer,
        string $corpus,
        ?TemplatingEngine $templating = null
    ) {
        $this->solarium = $solarium;
        $this->solariumQueryBuilder = $solariumQueryBuilder;
        $this->corpus = $corpus;
        $this->templating = $templating;
        $this->eventDispatcher = $eventDispatcher;
        $this->facetValueCanonicalizer = $facetValueCanonicalizer;
    }


    /**
     * {@inheritDoc}
     */
    public function executeQuery($query, ?SearchContextInterface $searchContext = null): ResultInterface
    {
        return $this->executeQueryAsync($query, $searchContext)->wait();
    }

    /**
     * {@inheritDoc}
     */
    public function executeQueryAsync($query, ?SearchContextInterface $searchContext = null)
    {
        return coroutine(
            function () use ($query, $searchContext) {
                if (!$query instanceof ResolvedSelectQueryInterface) {
                    if ($searchContext === null) {
                        $searchContext = new NoopSearchContext();
                    }

                    $query = new ResolvedSelectQuery(
                        $query,
                        $searchContext
                    );
                }

                if (!$query instanceof ResolvedSelectQueryInterface) {
                    throw new \InvalidArgumentException('$query must be of type ResolvedSelectQueryInterface or SelectQueryInterface');
                }

                $solariumQuery = $this->solariumQueryBuilder->buildSolariumQueryFromGeneric($query, $this->solarium->createSelect());

                //apply offset/limit
                $maxPerPage = $query->getMaxPerPage();
                $page = $query->getPageNumber();

                $solariumQuery->setRows($maxPerPage ?: self::INFINITY);

                if ($page && $maxPerPage) {
                    $solariumQuery->setStart($maxPerPage * ($page-1));
                }

                $pluginIndex = 'async';
                /** @var SolariumAsyncPlugin $asyncPlugin */
                $asyncPlugin = $this->solarium
                    ->registerPlugin($pluginIndex, new SolariumAsyncPlugin())
                    ->getPlugin($pluginIndex);


                // Short term keep as non-breaking change
                if (method_exists($asyncPlugin, 'setEventDispatcher')) {
                    $asyncPlugin->setEventDispatcher($this->eventDispatcher);
                }

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
                $result->setFacetSetStrategy(
                    new SolariumFacetSetsStrategy(
                        $resultClosure,
                        $query->getFacets(),
                        $query->getFacetCollatorProvider(),
                        $query->getFacetSetDecoratorProvider(),
                        $query->getOriginalSelectQuery(),
                        $this->facetValueCanonicalizer
                    )
                );

                //set any spellcheck result
                $result->setSpellcheckResultStrategy(new SolariumSpellcheckResultStrategy($resultClosure, $query));

                //set strategy for debug information output as this is not available through pagerfanta - only if templating service was available
                if (null !== $this->templating) {
                    $result->setDebugOutputStrategy(new SolariumDebugOutputStrategy($resultClosure, $this->templating));
                }

                $result->setMappingHashForFields($query->getMappingHashForFields());

                yield $result;
            }
        );
    }
}
