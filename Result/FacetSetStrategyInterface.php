<?php

namespace Markup\NeedleBundle\Result;

/**
 * An interface for a strategy for retrieving a set of facet sets for a result.
 **/
interface FacetSetStrategyInterface
{
    /**
     * @return \Markup\NeedleBundle\Facet\FacetSetInterface[]
     **/
    public function getFacetSets();
}
