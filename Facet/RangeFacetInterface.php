<?php

namespace Markup\NeedleBundle\Facet;

/**
 * An interface for a range facet, being a facet that shows ranged values.
 **/
interface RangeFacetInterface extends FacetInterface
{
    /**
     * Gets the start for the ranges that comprise this range facet.
     *
     * @return float
     **/
    public function getRangesStart();

    /**
     * Gets the end for the ranged that comprise this range facet.
     *
     * @return float
     **/
    public function getRangesEnd();

    /**
     * Gets the size of the facet ranges (i.e. if prices are showing 0-99, 100-199 etc, the size would be 100).
     *
     * @return float
     **/
    public function getRangeSize();
}
