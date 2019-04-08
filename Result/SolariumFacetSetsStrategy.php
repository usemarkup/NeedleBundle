<?php

namespace Markup\NeedleBundle\Result;

use Markup\NeedleBundle\Context\SearchContextInterface as SearchContext;
use Markup\NeedleBundle\Facet\FacetSetInterface;
use Markup\NeedleBundle\Query\SelectQueryInterface;
use Solarium\QueryType\Select\Result\Result as SolariumResult;

/**
* A strategy for fetching facet sets from a Solarium result.
*/
class SolariumFacetSetsStrategy implements FacetSetStrategyInterface
{
    /**
     * @var SolariumResult
     **/
    private $solariumResult;

    /**
     * A closure that returns a Solarium result object.
     *
     * @var \Closure
     **/
    private $solariumResultClosure;

    /**
     * @var SearchContext
     **/
    private $searchContext;

    /**
     * @var SelectQueryInterface|null
     */
    private $originalQuery;

    /**
     * @param SolariumResult|\Closure $result  This can either be a result object, or a closure that returns a result object.  (This enables support for deferred evaluation of the result.)
     * @param SearchContext           $context
     * @param SelectQueryInterface    $originalQuery (Optional.) An original query, if one is available, in case there is view logic that depends on it.
     **/
    public function __construct($result, SearchContext $context, SelectQueryInterface $originalQuery = null)
    {
        if ($result instanceof SolariumResult) {
            $this->solariumResult = $result;
        } elseif ($result instanceof \Closure) {
            $this->solariumResultClosure = $result;
        } else {
            throw new \InvalidArgumentException(sprintf('Passed an instance of %s as a result into %s. Expected a Solarium result instance (Solarium\QueryType\Select\Result\Result) or a closure that returns a Solarium result instance.', get_class($result), __METHOD__));
        }
        $this->searchContext = $context;
        $this->originalQuery = $originalQuery;
    }

    public function getFacetSets()
    {
        /** @var FacetSetInterface[] $facetSets */
        $facetSets = new SolariumFacetSetsIterator($this->getSolariumResult()->getFacetSet(), $this->getSearchContext(), $this->originalQuery);

        return $facetSets;
    }

    /**
     * @return SolariumResult
     **/
    private function getSolariumResult()
    {
        if (null === $this->solariumResult) {
            $this->solariumResult = $this->solariumResultClosure->__invoke();
        }

        return $this->solariumResult;
    }

    /**
     * @return SearchContext
     **/
    private function getSearchContext()
    {
        return $this->searchContext;
    }
}
