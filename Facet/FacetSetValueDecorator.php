<?php

namespace Markup\NeedleBundle\Facet;

/**
* A facet set decorator that applies decoration to facet values.
*/
class FacetSetValueDecorator extends FacetSetDecorator
{
    use EnsureIteratorTrait;

    /**
     * @var FacetValueDecoratorInterface
     **/
    private $facetValueDecorator;

    public function __construct(FacetValueDecoratorInterface $facetValueDecorator)
    {
        $this->facetValueDecorator = $facetValueDecorator;
    }

    public function getIterator()
    {
        return new FacetValueDecoratingIterator(
            $this->ensureIterator(parent::getIterator()),
            $this->getFacetValueDecorator()
        );
    }

    /**
     * @return FacetValueDecoratorInterface
     **/
    private function getFacetValueDecorator()
    {
        return $this->facetValueDecorator;
    }
}
