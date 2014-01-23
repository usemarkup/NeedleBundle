<?php

namespace Markup\NeedleBundle\Provider;

/**
 * An interface for a provider object that can provide filter objects.
 **/
interface FilterProviderInterface
{
    /**
     * Gets a filter object using a name.  Returns null if name does not correspond to known filter.
     *
     * @param  string                                               $name
     * @return \Markup\NeedleBundle\Filter\FilterInterface|null
     **/
    public function getFilterByName($name);
}
