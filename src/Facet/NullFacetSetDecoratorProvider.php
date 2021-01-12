<?php

namespace Markup\NeedleBundle\Facet;

use Markup\NeedleBundle\Attribute\AttributeInterface;

class NullFacetSetDecoratorProvider implements FacetSetDecoratorProviderInterface
{
    /**
     * Gets a facet set decorator for a provided facet, or returns null.
     *
     * @param AttributeInterface $facet
     * @return FacetSetDecoratorInterface|null
     */
    public function getDecoratorForFacet(AttributeInterface $facet)
    {
        return null;
    }
}
