<?php

namespace Markup\NeedleBundle\Filter;

/**
 * An interface for a provider object that can provide filter objects.
 *
 * @deprecated
 **/
interface FilterProviderInterface
{
    /**
     * Gets a filter object using a name.  Returns null if name does not correspond to known filter.
     *
     * @param  string                                               $name
     * @return \Markup\NeedleBundle\Attribute\AttributeInterface|null
     **/
    public function getFilterByName($name);
}
