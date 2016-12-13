<?php

namespace Markup\NeedleBundle\Attribute;

/**
 * A superclass for a decorator for attributes, setting up the default behaviour of a 1:1 map.
 */
abstract class SpecializedAttributeDecorator implements SpecializedAttributeInterface
{
    /**
     * The attribute being decorated.
     *
     * @var SpecializedAttributeInterface
     **/
    private $attribute;

    /**
     * @param SpecializedAttributeInterface $attribute
     **/
    public function __construct(SpecializedAttributeInterface $attribute)
    {
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
     * @return SpecializedAttributeInterface
     **/
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * {@inheritDoc}
     */
    public function getSpecialization()
    {
        return $this->attribute->getSpecialization();
    }

    /**
     * {@inheritDoc}
     */
    public function setContext(AttributeSpecializationContextInterface $context)
    {
        $this->attribute->setContext($context);
    }

    /**
     * {@inheritDoc}
     */
    public function getContext()
    {
        return $this->attribute->getContext();
    }

    public function __toString()
    {
        return $this->getDisplayName();
    }

}
