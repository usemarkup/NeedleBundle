<?php

namespace Markup\NeedleBundle\Facet;

use Markup\NeedleBundle\Filter\FilterInterface;

/**
 * An interface for a search facet.
 *
 * @deprecated
 **/
interface FacetInterface extends FilterInterface
{
    /**
     * Magic toString method.  Returns display name.
     *
     * @return string
     **/
    public function __toString();
}
