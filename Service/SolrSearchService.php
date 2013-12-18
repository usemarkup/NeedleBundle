<?php

namespace Markup\NeedleBundle\Service;

use Markup\NeedleBundle\Query\RecordableSelectQueryInterface;
use Markup\NeedleBundle\Query\SelectQueryInterface;
use Markup\NeedleBundle\Builder\SolariumSelectQueryBuilder;
use Markup\NeedleBundle\Result\PagerfantaResultAdapter;
use Markup\NeedleBundle\Result\SolariumDebugOutputStrategy;
use Markup\NeedleBundle\Result\SolariumFacetSetsStrategy;
use Markup\NeedleBundle\Context\SearchContextInterface;
use Pagerfanta\Adapter\SolariumAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Templating\EngineInterface as TemplatingEngine;
use Solarium\Client as SolariumClient;

/**
* A search service using Solr/ Solarium.
*/
class SolrSearchService implements SearchServiceInterface
{
    /**
     * Solr needs specification of a very large number for a 'view all' function.  An infinitely large number, in fact.  Like 600.
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
     * @param SolariumClient             $solarium
     * @param SolariumSelectQueryBuilder $solarium_query_builder
     **/
    public function __construct(SolariumClient $solarium, SolariumSelectQueryBuilder $solarium_query_builder, TemplatingEngine $templating = null)
    {
        $this->solarium = $solarium;
        $this->solariumQueryBuilder = $solarium_query_builder;
        $this->templating = $templating;
    }

    public function executeQuery(SelectQueryInterface $query)
    {
        $solariumQueryBuilder = $this->getSolariumQueryBuilder();
        if ($this->hasContext()) {
            $solariumQueryBuilder->setSearchContext($this->getContext());
        }
        $solariumQuery = $solariumQueryBuilder->buildSolariumQueryFromGeneric($query);
        $pagerfantaAdapter = new SolariumAdapter($this->getSolariumClient(), $solariumQuery);
        $pagerfanta = new Pagerfanta($pagerfantaAdapter);
        $maxPerPage = $query->getMaxPerPage();
        if (null === $maxPerPage && $this->hasContext()) {
            $maxPerPage = $this->getContext()->getItemsPerPage() ?: null;
        }
        $pagerfanta->setMaxPerPage($maxPerPage ?: self::INFINITY);
        $page = $query->getPageNumber();
        if ($page) {
            $pagerfanta->setCurrentPage($page, false, true);
        }

        $result = new PagerfantaResultAdapter($pagerfanta);
        $resultClosure = function() use ($pagerfantaAdapter) {
            return $pagerfantaAdapter->getResultSet();
        };

        //set the strategy to fetch facet sets, as these are not handled by pagerfanta
        if ($this->hasContext()) {
            $result->setFacetSetStrategy(new SolariumFacetSetsStrategy($resultClosure, $this->getContext(), ($query instanceof RecordableSelectQueryInterface) ? $query->getRecord() : null));
        }

        //set strategy for debug information output as this is not available through pagerfanta - only if templating service was available
        if (null !== $this->templating) {
            $result->setDebugOutputStrategy(new SolariumDebugOutputStrategy($resultClosure, $this->templating));
        }

        return $result;
    }

    public function setContext(SearchContextInterface $context)
    {
        $this->context = $context;
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
