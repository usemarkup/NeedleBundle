<?php

namespace Markup\NeedleBundle\Facet;

use Markup\NeedleBundle\Filter\SimpleFilterProvider;

/**
 * A simple facet provider implementation.
 */
class SimpleFacetProvider implements FacetProviderInterface
{
    /**
     * Gets a facet object using a name.  Returns null if name does not correspond to known facet.
     *
     * @param  string $name
     * @return \Markup\NeedleBundle\Facet\FacetInterface|null
     **/
    public function getFacetByName($name)
    {
        $filterProvider = new SimpleFilterProvider();

        return new FacetField($filterProvider->getFilterByName($name));
    }
}
