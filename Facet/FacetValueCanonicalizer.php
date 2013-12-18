<?php

namespace Markup\NeedleBundle\Facet;

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
    private $canonicalizerCollections = array();

    /**
     * {@inheritdoc}
     **/
    public function canonicalizeForFacet($value, FacetInterface $facet)
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
     * @return FacetValueCanonicalizerInterface[]
     **/
    private function getCanonicalizersForFacet(FacetInterface $facet)
    {
        if (!isset($this->canonicalizerCollections[$facet->getName()])) {
            return array();
        }

        return $this->canonicalizerCollections[$facet->getName()];
    }
}
