<?php

namespace Markup\NeedleBundle\Attribute;

/**
* A superclass for a decorator for attributes, setting up the default behaviour of a 1:1 map.
*/
abstract class AttributeDecorator implements AttributeInterface
{
    /**
     * The attribute being decorated.
     *
     * @var AttributeInterface
     **/
    private $attribute;

    /**
     * @param AttributeInterface $attribute
     **/
    public function __construct(AttributeInterface $attribute)
    {
        $this->attribute = $attribute;
    }

    public function getName()
    {
        return $this->attribute->getName();
    }

    public function getDisplayName()
    {
        return $this->attribute->getDisplayName();
    }

    public function getSearchKey(array $options = array())
    {
        return $this->attribute->getSearchKey($options);
    }

    public function __toString()
    {
        return $this->getDisplayName();
    }
}
