<?php

namespace Markup\NeedleBundle\Facet;

/**
 * An interface for an object that can canonicalize a facet value given a facet.
 **/
interface FacetValueCanonicalizerInterface
{
    /**
     * Canonicalizes a facet value string given a facet.
     *
     * @param  string         $value
     * @param  FacetInterface $facet
     * @return string
     **/
    public function canonicalizeForFacet($value, FacetInterface $facet);
}
