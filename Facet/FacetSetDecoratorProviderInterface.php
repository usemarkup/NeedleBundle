<?php

namespace Markup\NeedleBundle\Facet;

interface FacetSetDecoratorProviderInterface
{
    /**
     * Gets a facet set decorator for a provided facet, or returns null.
     *
     * @param FacetInterface $facet
     * @return FacetSetDecoratorInterface|null
     */
    public function getDecoratorForFacet(FacetInterface $facet);
} 
