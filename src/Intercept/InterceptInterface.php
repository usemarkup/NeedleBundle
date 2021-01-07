<?php

namespace Markup\NeedleBundle\Intercept;

/**
 * An interface for an object that represents a search intercept.
 **/
interface InterceptInterface
{
    /**
     * @return string
     **/
    public function getUri();
}
