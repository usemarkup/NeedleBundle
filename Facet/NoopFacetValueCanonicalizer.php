<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Facet;

use Markup\NeedleBundle\Attribute\AttributeInterface;

final class NoopFacetValueCanonicalizer implements FacetValueCanonicalizerInterface
{
    /**
     * {@inheritdoc}
     **/
    public function canonicalizeForFacet(string $value, AttributeInterface $facet): string
    {
        return $value;
    }
}
