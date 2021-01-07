<?php

namespace Markup\NeedleBundle\Facet;

use Markup\NeedleBundle\Attribute\AttributeInterface;

/**
* A generic facet set.
*/
class FacetSet implements FacetSetInterface
{
    /**
     * A search facet.
     *
     * @var AttributeInterface
     **/
    private $facet;

    /**
     * An iterator over an ordered collection of facet values.
     *
     * @var FacetSetIteratorInterface
     **/
    private $facetSetIterator;

    /**
     * @param AttributeInterface        $facet
     * @param FacetSetIteratorInterface $facetSetIterator
     **/
    public function __construct(AttributeInterface $facet, FacetSetIteratorInterface $facetSetIterator)
    {
        $this->facet = $facet;
        $this->facetSetIterator = $facetSetIterator;
    }

    public function getFacet()
    {
        return $this->facet;
    }

    public function getIterator()
    {
        return $this->facetSetIterator;
    }

    public function count()
    {
        return $this->getIterator()->count();
    }
}
