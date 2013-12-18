<?php

namespace Markup\NeedleBundle\Filter;

/**
* A filter query object.
*/
class FilterQuery implements FilterQueryInterface
{
    /**
     * @var FilterInterface
     **/
    private $filter;

    /**
     * @var FilterValueInterface
     **/
    private $filterValue;

    /**
     * @param FilterInterface      $filter
     * @param FilterValueInterface $filter_value
     **/
    public function __construct(FilterInterface $filter, FilterValueInterface $filter_value)
    {
        $this->filter = $filter;
        $this->filterValue = $filter_value;
    }

    public function getSearchKey()
    {
        return $this->filter->getSearchKey();
    }

    public function getSearchValue()
    {
        return $this->filterValue->getSearchValue();
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function getFilterValue()
    {
        return $this->filterValue;
    }
}
