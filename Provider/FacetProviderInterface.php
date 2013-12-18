<?php

namespace Markup\NeedleBundle\Provider;

/**
 * An interface for a provider object that can provide facet objects.
 **/
interface FacetProviderInterface
{
    /**
     * Gets a facet object using a name.  Returns false if name does not correspond to known facet.
     *
     * @param  string                                             $name
     * @return \Markup\NeedleBundle\Facet\FacetInterface|bool
     **/
    public function getFacetByName($name);
}
