<?php

namespace Markup\NeedleBundle\Facet;

/**
 * An interface for a facet set decorator.
 **/
interface FacetSetDecoratorInterface extends FacetSetInterface
{
    /**
     * Decorates a facet set.
     *
     * @param  FacetSetInterface $facetSet
     * @return self
     **/
    public function decorate(FacetSetInterface $facetSet);
}
