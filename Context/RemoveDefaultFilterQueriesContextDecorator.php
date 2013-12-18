<?php

namespace Markup\NeedleBundle\Context;

use Markup\NeedleBundle\Facet\FacetInterface;
use Markup\NeedleBundle\Query\SelectQueryInterface;

/**
* A search context decorator that has no default filter queries.
*/
class RemoveDefaultFilterQueriesContextDecorator implements SearchContextInterface
{
    const LARGE_NUMBER = 10000;

    /**
     * @var SearchContextInterface
     **/
    private $searchContext;

    /**
     * @param SearchContextInterface $searchContext The search context being decorated.
     **/
    public function __construct(SearchContextInterface $searchContext)
    {
        $this->searchContext = $searchContext;
    }

    public function getDefaultFilterQueries()
    {
        return array();
    }

    public function getDefaultSortCollectionForQuery(SelectQueryInterface $query)
    {
        return $this->searchContext->getDefaultSortCollectionForQuery($query);
    }

    public function getItemsPerPage()
    {
        return self::LARGE_NUMBER;
    }

    public function getFacets()
    {
        return $this->searchContext->getFacets();
    }

    public function getSetDecoratorForFacet(FacetInterface $facet)
    {
        return $this->searchContext->getSetDecoratorForFacet($facet);
    }

    public function getWhetherFacetIgnoresCurrentFilters(FacetInterface $facet)
    {
        return $this->searchContext->getWhetherFacetIgnoresCurrentFilters($facet);
    }

    public function shouldUseFacetValuesForRecordedQuery()
    {
        return $this->searchContext->shouldUseFacetValuesForRecordedQuery();
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
