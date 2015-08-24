<?php

namespace Markup\NeedleBundle\Attribute;

/**
 * Provides a value which when combined with an attribute allows an attribute to be specialized
 */
interface AttributeSpecializationContextInterface
{
    /**
     * The value of the context
     * @return string
     */
    public function getValue();

    /**
     * The underlying data that is used to get the value
     * @return mixed
     */
    public function getData();
}
