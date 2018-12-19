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

    public function __construct(AttributeInterface $filter, FilterValueInterface $filterValue)
    {
        $this->filter = $filter;
        $this->filterValue = $filterValue;
    }

    public function getSearchKey()
    {
        return $this->filter->getSearchKey(['prefer_parsed' => false]);
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

    public function getValueType(): string
    {
        return $this->getFilterValue()->getValueType();
    }
}
