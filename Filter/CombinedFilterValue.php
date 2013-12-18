<?php

namespace Markup\NeedleBundle\Filter;

/**
* A filter value that represents the combination of other filter values.
*/
abstract class CombinedFilterValue implements CombinedFilterValueInterface
{
    /**
     * A collection of filter values for which this is the union.
     *
     * @var FilterValueInterface[]
     **/
    private $filterValues;

    /**
     * @param array $filterValues
     **/
    public function __construct($filterValues)
    {
        $this->filterValues = $filterValues;
    }

    public function getSearchValue()
    {
        $filterValues = $this->getValues();
        if (count($filterValues) == 1) {
            foreach ($filterValues as $filterValue) {
                break;
            }

            return $filterValue->getSearchValue();
        }

        return sprintf(
            '(%s)',
            implode(' ', array_map(function($filterValue) { return $filterValue->getSearchValue(); }, $filterValues))
            );
    }

    public function getSlug()
    {
        $filterValues = $this->getValues();

        return implode('::', array_map(function($filterValue) { return $filterValue->getSearchValue(); }, $filterValues));
    }

    public function getValues()
    {
        return $this->filterValues;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->getValues());
    }

    public function addFilterValue(FilterValueInterface $filterValue)
    {
        $this->filterValues[] = $filterValue;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->filterValues);
    }
}
