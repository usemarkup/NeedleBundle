<?php

namespace Markup\NeedleBundle\Filter;

/**
 * A simple filter provider implementation.
 */
class SimpleFilterProvider implements FilterProviderInterface
{
    /**
     * Gets a filter object using a name.  Returns null if name does not correspond to known filter.
     *
     * @param  string $name
     * @return \Markup\NeedleBundle\Attribute\AttributeInterface|null
     **/
    public function getFilterByName($name)
    {
        return new SimpleFilter($name);
    }
}
