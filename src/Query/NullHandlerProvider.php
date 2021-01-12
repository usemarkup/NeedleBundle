<?php

namespace Markup\NeedleBundle\Query;

class NullHandlerProvider implements HandlerProviderInterface
{
    /**
     * @return string|null
     */
    public function getHandler()
    {
        return null;
    }
}
