<?php

namespace Markup\NeedleBundle\Attribute;

/**
 * An implementation of a specialization context that doesn't provide a meaningful context
 * A null implementation
 */
class AttributeNullSpecializationContext implements AttributeSpecializationNullContextInterface
{
    /**
     * {@inheritDoc}
     */
    public function getValue()
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function getData()
    {
        return;
    }
}
