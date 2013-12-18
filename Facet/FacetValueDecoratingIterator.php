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
     * @param \Markup\NeedleBundle\Facet\FacetValueInterface[] $facet_value_iterator
     * @param FacetValueDecoratorInterface                          $facet_value_decorator
     **/
    public function __construct(\Iterator $facet_value_iterator, FacetValueDecoratorInterface $facet_value_decorator)
    {
        $this->facetValueIterator = $facet_value_iterator;
        $this->facetValueDecorator = $facet_value_decorator;
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
