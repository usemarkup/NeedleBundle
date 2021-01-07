<?php

namespace Markup\NeedleBundle\Query;

/**
 * Interface for an object that provides a handler string in a context.
 */
interface HandlerProviderInterface
{
    /**
     * @return string|null
     */
    public function getHandler();
}
