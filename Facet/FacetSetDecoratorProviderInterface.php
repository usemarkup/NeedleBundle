<?php

namespace Markup\NeedleBundle\Facet;

use Markup\NeedleBundle\Attribute\AttributeInterface;

interface FacetSetDecoratorProviderInterface
{
    /**
     * Gets a facet set decorator for a provided facet, or returns null.
     *
     * @param AttributeInterface $facet
     * @return FacetSetDecoratorInterface|null
     */
    public function getDecoratorForFacet(AttributeInterface $facet);
}
