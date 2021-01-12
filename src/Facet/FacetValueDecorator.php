<?php

namespace Markup\NeedleBundle\Facet;

/**
* A base class for a facet value decorator.
*/
abstract class FacetValueDecorator implements FacetValueDecoratorInterface
{
    /**
     * The facet value being decorated.
     *
     * @var FacetValueInterface
     **/
    private $facetValue;

    public function getValue()
    {
        if (!$this->hasFacetValue()) {
            $this->throwBadMethodCallException(__METHOD__);
        }

        return $this->facetValue->getValue();
    }

    public function count()
    {
        if (!$this->hasFacetValue()) {
            $this->throwBadMethodCallException(__METHOD__);
        }

        return $this->facetValue->count();
    }

    public function __toString()
    {
        if (!$this->hasFacetValue()) {
            $this->throwBadMethodCallException(__METHOD__);
        }

        return $this->facetValue->__toString();
    }

    public function decorate(FacetValueInterface $facetValue)
    {
        $this->facetValue = $facetValue;

        return $this;
    }

    private function throwBadMethodCallException($method)
    {
        throw new \BadMethodCallException(sprintf('Called %s on a facet value decorator that was not decorating anything.', $method));
    }

    /**
     * Gets whether a facet value has been set on this decorator.
     *
     * @return bool
     **/
    protected function hasFacetValue()
    {
        return null !== $this->facetValue;
    }

    protected function getFacetValue()
    {
        return $this->facetValue;
    }
}
