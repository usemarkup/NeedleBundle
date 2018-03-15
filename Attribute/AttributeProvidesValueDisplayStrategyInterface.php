<?php

namespace Markup\NeedleBundle\Attribute;

/**
 * Interface for an attribute that can provide a display strategy for values.
 */
interface AttributeProvidesValueDisplayStrategyInterface
{
    /**
     * Gets a display strategy closure which takes a value as a parameter and emits a displayable value.
     *
     * @return \Closure|null
     */
    public function getValueDisplayStrategy();
}
