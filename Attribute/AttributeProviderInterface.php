<?php

namespace Markup\NeedleBundle\Attribute;

/**
 * Interface for an attribute provider that can provide an attribute when given a name.
 */
interface AttributeProviderInterface
{
    /**
     * Gets an attribute using a name.  Returns null if name does not correspond to known attribute.
     *
     * @param string $name
     * @return AttributeInterface
     */
    public function getAttributeByName($name);
} 
