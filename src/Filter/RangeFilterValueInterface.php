<?php

namespace Markup\NeedleBundle\Filter;

/**
 * A base interface for a filter value that represents a range.
 **/
interface RangeFilterValueInterface extends FilterValueInterface
{
    /**
     * @return float
     **/
    public function getMin();

    /**
     * @return float
     **/
    public function getMax();
}
