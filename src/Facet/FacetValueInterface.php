<?php

namespace Markup\NeedleBundle\Facet;

/**
 * An interface for a facet value.
 **/
interface FacetValueInterface extends \Countable
{
    /**
     * Gets the value string for this facet value.
     *
     * @return string
     **/
    public function getValue();

    /**
     * Gets the string to render/display for this value.
     *
     * @return string
     **/
    public function getDisplayValue();

    /**
     * Magic toString.
     *
     * @return string
     **/
    public function __toString();
}
