<?php

namespace Markup\NeedleBundle\Facet;

use Markup\NeedleBundle\Attribute\AttributeInterface;

/**
* A canonicalizer for facet values.
*/
class FacetValueCanonicalizer implements FacetValueCanonicalizerInterface
{
    /**
     * A collection (keyed by facet name) of collections of canonicalizers.
     *
     * @var array
     **/
    private $canonicalizerCollections = [];

    /**
     * {@inheritdoc}
     **/
    public function canonicalizeForFacet($value, AttributeInterface $facet)
    {
        foreach ($this->getCanonicalizersForFacet($facet) as $canonicalizer) {
            $value = $canonicalizer->canonicalizeForFacet($value, $facet);
        }

        return $value;
    }

    /**
     * Adds a canonicalizer for a given facet name.
     *
     * @param  FacetValueCanonicalizerInterface $canonicalizer
     * @param  string                           $facetName
     * @return self
     **/
    public function addCanonicalizerForFacetName(FacetValueCanonicalizerInterface $canonicalizer, $facetName)
    {
        if (!isset($this->canonicalizerCollections[$facetName])) {
            $this->canonicalizerCollections[$facetName] = new \SplObjectStorage();
        }
        $collection = $this->canonicalizerCollections[$facetName];
        $collection->attach($canonicalizer);

        return $this;
    }

    /**
     * Gets the registered canonicalizers for the provided facet.
     *
     * @param AttributeInterface $facet
     * @return FacetValueCanonicalizerInterface[]
     **/
    private function getCanonicalizersForFacet(AttributeInterface $facet)
    {
        if (!isset($this->canonicalizerCollections[$facet->getName()])) {
            return [];
        }

        return $this->canonicalizerCollections[$facet->getName()];
    }
}
