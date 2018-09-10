<?php

namespace Markup\NeedleBundle\Facet;

/**
* An outer iterator that wraps a facet value iterator and applies a decorator to each value.
*/
class FacetValueDecoratingIterator implements \OuterIterator
{
    /**
     * An iteration of facet values.
     *
     * @var \Iterator
     **/
    private $facetValueIterator;

    /**
     * @var FacetValueDecoratorInterface
     **/
    private $facetValueDecorator;

    /**
     * @param \Iterator|\Markup\NeedleBundle\Facet\FacetValueInterface[] $facetValueIterator
     * @param FacetValueDecoratorInterface                               $facetValueDecorator
     **/
    public function __construct(\Iterator $facetValueIterator, FacetValueDecoratorInterface $facetValueDecorator)
    {
        $this->facetValueIterator = $facetValueIterator;
        $this->facetValueDecorator = $facetValueDecorator;
    }

    public function getInnerIterator()
    {
        return $this->facetValueIterator;
    }

    public function current()
    {
        return $this->facetValueDecorator->decorate($this->getInnerIterator()->current());
    }

    public function next()
    {
        return $this->getInnerIterator()->next();
    }

    public function key()
    {
        return $this->getInnerIterator()->key();
    }

    public function valid()
    {
        return $this->getInnerIterator()->valid();
    }

    public function rewind()
    {
        return $this->getInnerIterator()->rewind();
    }
}
