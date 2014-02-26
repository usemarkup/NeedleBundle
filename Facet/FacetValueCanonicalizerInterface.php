<?php

namespace Markup\NeedleBundle\Facet;

use Markup\NeedleBundle\Attribute\AttributeInterface;

/**
 * An interface for an object that can canonicalize a facet value given a facet.
 **/
interface FacetValueCanonicalizerInterface
{
    /**
     * Canonicalizes a facet value string given a facet.
     *
     * @param  string             $value
     * @param  AttributeInterface $facet
     * @return string
     **/
    public function canonicalizeForFacet($value, AttributeInterface $facet);
}
