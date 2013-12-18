<?php

namespace Markup\NeedleBundle\Facet;

/**
* A facet set decorator that applies decoration to facet values.
*/
class FacetSetValueDecorator extends FacetSetDecorator
{
    /**
     * @var FacetValueDecoratorInterface
     **/
    private $facetValueDecorator;

    /**
     * @param FacetValueDecoratorInterface $facet_value_decorator
     **/
    public function __construct(FacetValueDecoratorInterface $facet_value_decorator)
    {
        $this->facetValueDecorator = $facet_value_decorator;
    }

    public function getIterator()
    {
        return new FacetValueDecoratingIterator(parent::getIterator(), $this->getFacetValueDecorator());
    }

    /**
     * @return FacetValueDecoratorInterface
     **/
    private function getFacetValueDecorator()
    {
        return $this->facetValueDecorator;
    }
}
