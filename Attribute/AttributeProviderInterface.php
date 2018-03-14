<?php

namespace Markup\NeedleBundle\Attribute;

use Markup\NeedleBundle\Exception\MissingAttributeException;

/**
 * Interface for an attribute provider that can provide an attribute when given a name or search key
 */
interface AttributeProviderInterface
{
    /**
     * Gets an attribute using a name.  Returns null if name does not correspond to known attribute.
     *
     * @param string $name
     * @return AttributeInterface
     * @throws MissingAttributeException
     */
    public function getAttributeByName($name);

    /**
     * @param string $name
     * @return AttributeInterface|null
     */
    public function getAttributeBySearchKey($name);
} 
