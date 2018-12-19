<?php

namespace Markup\NeedleBundle\Filter;

/**
* A filter value that represents the union of other filter values.
*/
class UnionFilterValue extends CombinedFilterValue implements UnionFilterValueInterface
{
    /**
     * A collection of filter values for which this is the union.
     *
     * @var FilterValueInterface[]
     * @deprecated This instance variable, and the other methods, should be removed from this class. Variable only in place for compatibility with serialised objects stored elsewhere.
     **/
    private $filterValues;

    public function __construct($filterValues)
    {
        $this->filterValues = $filterValues;
        parent::__construct($filterValues);
    }

    public function getValues()
    {
        return $this->filterValues;
    }

    public function addFilterValue(FilterValueInterface $filterValue)
    {
        $this->filterValues[] = $filterValue;
    }

    public function count()
    {
        return count($this->filterValues);
    }

    public function getValueType(): string
    {
        return self::TYPE_UNION;
    }
}
