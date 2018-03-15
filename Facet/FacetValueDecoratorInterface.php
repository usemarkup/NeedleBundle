<?php

namespace Markup\NeedleBundle\Facet;

/**
 * An interface for a decorator for facet values.
 **/
interface FacetValueDecoratorInterface extends FacetValueInterface
{
    /**
     * Decorates a provided facet value.
     *
     * @param  FacetValueInterface $facetValue
     * @return self
     **/
    public function decorate(FacetValueInterface $facetValue);
}
