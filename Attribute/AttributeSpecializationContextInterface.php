<?php

namespace Markup\NeedleBundle\Attribute;

/**
 * Provides a value which when combined with an attribute allows an attribute to be specialized
 */
interface AttributeSpecializationContextInterface
{
    /**
     * The value of the context
     * @return mixed
     */
    public function getValue();
}
