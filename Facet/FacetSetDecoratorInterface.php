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
     * @param  FacetSetInterface $facet_set
     * @return self
     **/
    public function decorate(FacetSetInterface $facet_set);
}
