<?php

namespace Markup\NeedleBundle\Filter;

use Markup\NeedleBundle\Attribute\AttributeInterface;

/**
* A filter query object.
*/
class FilterQuery implements FilterQueryInterface
{
    /**
     * @var AttributeInterface
     **/
    private $filter;

    /**
     * @var FilterValueInterface
     **/
    private $filterValue;

    /**
     * @param AttributeInterface   $filter
     * @param FilterValueInterface $filter_value
     **/
    public function __construct(AttributeInterface $filter, FilterValueInterface $filter_value)
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
