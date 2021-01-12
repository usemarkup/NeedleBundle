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
        if ($attribute instanceof SpecializedAttributeInterface) {
            throw new \Exception('Use SpecializedDecorators when decorating specialized attributes');
        }
        $this->attribute = $attribute;
    }

    /**
     * {@inheritDoc}
     **/
    public function getName()
    {
        return $this->attribute->getName();
    }

    /**
     * {@inheritDoc}
     **/
    public function getDisplayName()
    {
        return $this->attribute->getDisplayName();
    }

    /**
     * {@inheritDoc}
     **/
    public function getSearchKey(array $options = [])
    {
        return $this->attribute->getSearchKey($options);
    }

    /**
     * Returns the underlying attribute that is being decorated
     * @return AttributeInterface
     **/
    public function getAttribute()
    {
        return $this->attribute;
    }

    public function __toString()
    {
        return $this->getDisplayName();
    }
}
