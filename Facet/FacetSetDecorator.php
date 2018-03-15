<?php

namespace Markup\NeedleBundle\Facet;

/**
* A base class for a facet set decorator.
*/
abstract class FacetSetDecorator implements FacetSetDecoratorInterface
{
    /**
     * The facet set being decorated.
     *
     * @var FacetSetInterface
     **/
    protected $facetSet = null;

    public function getFacet()
    {
        if (!$this->hasFacetSet()) {
            $this->throwBadMethodCallException(__METHOD__);
        }

        return $this->facetSet->getFacet();
    }

    public function getIterator()
    {
        if (!$this->hasFacetSet()) {
            $this->throwBadMethodCallException(__METHOD__);
        }

        return $this->facetSet->getIterator();
    }

    public function count()
    {
        if (!$this->hasFacetSet()) {
            $this->throwBadMethodCallException(__METHOD__);
        }

        return $this->facetSet->count();
    }

    public function decorate(FacetSetInterface $facetSet)
    {
        $this->facetSet = $facetSet;

        return $this;
    }

    private function throwBadMethodCallException($method)
    {
        throw new \BadMethodCallException(sprintf('Called %s on a facet set decorator that was not decorating anything.', $method));
    }

    /**
     * Gets whether a facet set has been set on this decorator.
     *
     * @return bool
     **/
    protected function hasFacetSet()
    {
        return null !== $this->facetSet;
    }

    protected function getFacetSet()
    {
        return $this->facetSet;
    }
}
