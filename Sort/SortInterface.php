<?php

namespace Markup\NeedleBundle\Sort;

/**
 * A base interface for a sort.
 **/
interface SortInterface
{
    /**
     * Gets the filter being sorted on.
     *
     * @return \Markup\NeedleBundle\Attribute\AttributeInterface
     **/
    public function getFilter();

    /**
     * Gets whether this sort is descending.
     *
     * @return bool
     **/
    public function isDescending();
}
