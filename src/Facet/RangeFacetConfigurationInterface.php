<?php

namespace Markup\NeedleBundle\Facet;

/**
 * An interface for a configuration for a range facet.
 **/
interface RangeFacetConfigurationInterface
{
    /**
     * Gets the start for the ranges for the facet.
     *
     * @return float
     **/
    public function getStart();

    /**
     * Gets the end for the ranges for the facet.
     *
     * @return float
     **/
    public function getEnd();

    /**
     * Gets the gap between range bounds (range size).
     *
     * @return float
     **/
    public function getGap();
}
