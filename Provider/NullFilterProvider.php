<?php

namespace Markup\NeedleBundle\Provider;

class NullFilterProvider implements FilterProviderInterface
{
    /**
     * Gets a filter object using a name.  Returns false if name does not correspond to known filter.
     *
     * @param  string $name
     * @return \Markup\NeedleBundle\Filter\FilterInterface|bool
     **/
    public function getFilterByName($name)
    {
        return false;
    }
}
