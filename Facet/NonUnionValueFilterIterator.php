<?php

namespace Markup\NeedleBundle\Facet;

use Markup\NeedleBundle\Filter\CombinedFilterValueInterface;
use Markup\NeedleBundle\Query\SelectQueryInterface;

/**
 * A filter iterator that, if a facet has a combined filter value, will exclude any values that are not within the combined filter value.
 */
class NonUnionValueFilterIterator extends \FilterIterator
{
    /**
     * @var FacetSetInterface
     */
    private $facetSet;

    /**
     * @var SelectQueryInterface
     */
    private $originalQuery;

    /**
     * @param \Iterator            $facetValues
     * @param SelectQueryInterface $originalQuery
     */
    public function __construct(\Iterator $facetValues, FacetSetInterface $facetSet, SelectQueryInterface $originalQuery = null)
    {
        parent::__construct($facetValues);
        $this->facetSet = $facetSet;
        $this->originalQuery = $originalQuery;
    }

    public function accept()
    {
        if (null === $this->originalQuery) {
            return true;
        }
        $facetValue = $this->getInnerIterator()->current();
        $filterQuery = $this->originalQuery->getFilterQueryWithKey($this->getCurrentFacetKey());
        if (!$filterQuery) {
            return true;
        }
        if (!$filterQuery->getFilterValue() instanceof CombinedFilterValueInterface || count($filterQuery->getFilterValue()) === 1) {
            return true;
        }
        foreach ($filterQuery->getFilterValue() as $filterValue) {
            if ($facetValue->getValue() === $filterValue->getSearchValue()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    private function getCurrentFacetKey()
    {
        return $this->facetSet->getFacet()->getSearchKey();
    }
}
