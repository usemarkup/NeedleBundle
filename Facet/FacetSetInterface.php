<?php

namespace Markup\NeedleBundle\Facet;

interface FacetSetInterface extends \IteratorAggregate, \Countable
{
    /**
     * Gets the facet that this facet set pertains to.
     *
     * @return \Markup\NeedleBundle\Facet\FacetInterface
     **/
    public function getFacet();
}
