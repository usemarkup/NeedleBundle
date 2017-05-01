<?php

namespace Markup\NeedleBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Markup\NeedleBundle\Adapter\SolariumGroupedQueryPagerfantaAdapter;
use Markup\NeedleBundle\Builder\SolariumSelectQueryBuilder;
use Markup\NeedleBundle\Context\SearchContextInterface;
use Markup\NeedleBundle\Query\ResolvedSelectQuery;
use Markup\NeedleBundle\Query\ResolvedSelectQueryDecoratorInterface;
use Markup\NeedleBundle\Query\SelectQueryInterface;
use Markup\NeedleBundle\Result\PagerfantaResultAdapter;
use Markup\NeedleBundle\Result\SolariumDebugOutputStrategy;
use Markup\NeedleBundle\Result\SolariumFacetSetsStrategy;
use Markup\NeedleBundle\Result\SolariumSpellcheckResultStrategy;
use Pagerfanta\Adapter\SolariumAdapter;
use Pagerfanta\Pagerfanta;
use Solarium\Client as SolariumClient;
use Symfony\Component\Templating\EngineInterface as TemplatingEngine;

/**
 * A search service using Solr/ Solarium.
 *
 * Composes a SelectQuery & SearchContext (into a ResolvedQuery), converts it into a solr query and
 * executes the query (using Solarium) and then returns a result (as a PagerfantaResultAdapter)
 */
class SolrSearchService implements SearchServiceInterface
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
        $solariumQueryBuilder = $this->getSolariumQueryBuilder();

        $query = new ResolvedSelectQuery($query, $this->hasContext() ? $this->getContext() : null);

        foreach ($this->decorators as $decorator) {
            $query = $decorator->decorate($query);
        }

        $solariumQuery = $solariumQueryBuilder->buildSolariumQueryFromGeneric($query);

        if ($query->getGroupingField()) {
            $pagerfantaAdapter = new SolariumGroupedQueryPagerfantaAdapter($this->getSolariumClient(), $solariumQuery);
        } else {
            $pagerfantaAdapter = new SolariumAdapter($this->getSolariumClient(), $solariumQuery);
        }

        $pagerfanta = new Pagerfanta($pagerfantaAdapter);
        $maxPerPage = $query->getMaxPerPage();
        if (null === $maxPerPage && $this->hasContext() && $query->getPageNumber() !== null) {
            $maxPerPage = $this->getContext()->getItemsPerPage() ?: null;
        }
        $pagerfanta->setMaxPerPage($maxPerPage ?: self::INFINITY);
        $page = $query->getPageNumber();
        if ($page) {
            $pagerfanta->setCurrentPage($page, false, true);
        }

        $result = new PagerfantaResultAdapter($pagerfanta);
        $resultClosure = function () use ($pagerfantaAdapter) {
            return $pagerfantaAdapter->getResultSet();
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

        return $result;
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
