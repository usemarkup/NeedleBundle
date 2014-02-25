<?php

namespace Markup\NeedleBundle\Context;

use Markup\NeedleBundle\Facet\FacetInterface;
use Markup\NeedleBundle\Facet\FacetProviderInterface;
use Markup\NeedleBundle\Query\SelectQueryInterface;

/**
* A search context decorator that makes the context use the available filter names for facets.
*/
class UseAvailableFiltersAsFacetsContextDecorator implements SearchContextInterface
{
    /**
     * The context being decorated.
     *
     * @var SearchContextInterface
     **/
    private $searchContext;

    /**
     * @var FacetProviderInterface
     **/
    private $facetProvider;

    /**
     * @param SearchContextInterface $searchContext
     * @param FacetProviderInterface $facetProvider
     **/
    public function __construct(SearchContextInterface $searchContext, FacetProviderInterface $facetProvider)
    {
        $this->searchContext = $searchContext;
        $this->facetProvider = $facetProvider;
    }

    public function getFacets()
    {
        $facets = array();
        foreach ($this->getAvailableFilterNames() as $filterName) {
            $facets[] = $this->facetProvider->getFacetByName($filterName);
        }

        return $facets;
    }

    public function getItemsPerPage()
    {
        return $this->searchContext->getItemsPerPage();
    }

    public function getDefaultFilterQueries()
    {
        return $this->searchContext->getDefaultFilterQueries();
    }

    public function getDefaultSortCollectionForQuery(SelectQueryInterface $query)
    {
        return $this->searchContext->getDefaultSortCollectionForQuery($query);
    }

    public function getSetDecoratorForFacet(FacetInterface $facet)
    {
        return $this->searchContext->getSetDecoratorForFacet($facet);
    }

    public function getWhetherFacetIgnoresCurrentFilters(FacetInterface $facet)
    {
        return $this->searchContext->getWhetherFacetIgnoresCurrentFilters($facet);
    }

    public function getAvailableFilterNames()
    {
        return $this->searchContext->getAvailableFilterNames();
    }

    public function getBoostQueryFields()
    {
        return $this->searchContext->getBoostQueryFields();
    }

    public function getFacetCollatorProvider()
    {
        return $this->searchContext->getFacetCollatorProvider();
    }

    public function getFacetSortOrderProvider()
    {
        return $this->searchContext->getFacetSortOrderProvider();
    }

    public function getInterceptor()
    {
        return $this->searchContext->getInterceptor();
    }
}
