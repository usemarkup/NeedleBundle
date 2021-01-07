<?php

namespace Markup\NeedleBundle\Attribute;

use Markup\NeedleBundle\Exception\IllegalContextValueException;

/**
 * Provides a value which when combined with an attribute allows an attribute to be specialized
 */
interface AttributeSpecializationContextInterface
{
    /**
     * The value of the context
     * @return string
     * @throws IllegalContextValueException
     */
    public function getValue(): string;

    /**
     * The underlying data that is used to get the value
     * @return mixed
     */
    public function getData();
}
