<?php

namespace Markup\NeedleBundle\Context;

use Markup\NeedleBundle\Facet\FacetInterface;
use Markup\NeedleBundle\Facet\NullSortOrderProvider;
use Markup\NeedleBundle\Intercept\NullInterceptor;
use Markup\NeedleBundle\Provider\NullCollatorProvider;
use Markup\NeedleBundle\Query\SelectQueryInterface;
use Markup\NeedleBundle\Sort\EmptySortCollection;

/**
* A context for searches, providing information that can determine how searches display, which is agnostic of search engine implementations.
*/
class SearchContext implements SearchContextInterface
{
    /**
     * @var int
     **/
    private $itemsPerPage;

    /**
     * @param int $items_per_page
     **/
    public function __construct($items_per_page)
    {
        $this->itemsPerPage = $items_per_page;
    }

    /**
     * Gets the number of items to be used on a page.
     *
     * @return int
     **/
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * Gets the set of facets that should be requested with this context.
     *
     * @return \Markup\NeedleBundle\Facet\FacetInterface[]
     **/
    public function getFacets()
    {
        return array();
    }

    public function getDefaultFilterQueries()
    {
        return array();
    }

    public function getDefaultSortCollectionForQuery(SelectQueryInterface $query)
    {
        return new EmptySortCollection();
    }

    public function getWhetherFacetIgnoresCurrentFilters(FacetInterface $facet)
    {
        return false;
    }

    public function shouldUseFacetValuesForRecordedQuery()
    {
        return false;
    }

    public function getSetDecoratorForFacet(FacetInterface $facet)
    {
        return false;
    }

    public function getAvailableFilterNames()
    {
        return array();
    }

    public function getBoostQueryFields()
    {
        return array();
    }

    public function getFacetCollatorProvider()
    {
        return new NullCollatorProvider();
    }

    public function getFacetSortOrderProvider()
    {
        return new NullSortOrderProvider();
    }

    public function getInterceptor()
    {
        return new NullInterceptor();
    }
}
