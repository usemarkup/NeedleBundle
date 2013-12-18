<?php

namespace Markup\NeedleBundle\Facet;

/**
 * An interface for a composite facet set
 **/
interface CompositeFacetSetInterface extends FacetSetInterface
{
    /**
     * Gets the set of facet sets this composite composes.
     *
     * @return array
     **/
    public function getSubFacetSets();
}
