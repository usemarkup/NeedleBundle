<?php

namespace Markup\NeedleBundle\Provider;

class NullFilterProvider implements FilterProviderInterface
{
    /**
     * Gets a filter object using a name.  Returns null if name does not correspond to known filter.
     *
     * @param  string $name
     * @return \Markup\NeedleBundle\Filter\FilterInterface|null
     **/
    public function getFilterByName($name)
    {
        return null;
    }
}
