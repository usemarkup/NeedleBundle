<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Facet;

use Markup\NeedleBundle\Attribute\AttributeInterface;

/**
 * An interface for an object that can canonicalize a facet value given a facet.
 **/
interface FacetValueCanonicalizerInterface
{
    public function canonicalizeForFacet(string $value, AttributeInterface $facet): string;
}
