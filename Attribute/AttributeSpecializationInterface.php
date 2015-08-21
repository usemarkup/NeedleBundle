<?php

namespace Markup\NeedleBundle\Attribute;

/**
 * A specialization that can be applied to an attribute
 */
interface AttributeSpecializationInterface
{
    /**
     * @return string
     */
    public function getName();
}
