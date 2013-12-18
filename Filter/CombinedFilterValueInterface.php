<?php

namespace Markup\NeedleBundle\Filter;

/**
 * An interface for a filter value that represents the combination of other filter values.
 **/
interface CombinedFilterValueInterface extends FilterValueInterface, \IteratorAggregate, \Countable
{
    /**
     * Gets the series of filter values that this filter value represents the combination of.
     *
     * @return FilterValueInterface[]
     **/
    public function getValues();

    /**
     * Adds the provided filter value to the combination.
     *
     * @param FilterValueInterface $filterValue
     **/
    public function addFilterValue(FilterValueInterface $filterValue);
}
