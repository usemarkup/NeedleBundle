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
     * @var SpecializedAttributeInterface&AttributeInterface
     **/
    private $attribute;

    /**
     * @param SpecializedAttributeInterface $attribute
     **/
    public function __construct(SpecializedAttributeInterface $attribute)
    {
        if (!$attribute instanceof AttributeInterface) {
            throw new \LogicException('Attribute passed of unexpected type.');
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
     * @return SpecializedAttributeInterface
     **/
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * {@inheritDoc}
     */
    public function getSpecializations()
    {
        return $this->attribute->getSpecializations();
    }

    /**
     * {@inheritDoc}
     */
    public function setContext(
        AttributeSpecializationContextInterface $context,
        string $specialization
    ) {
        $this->attribute->setContext($context, $specialization);
    }

    /**
     * {@inheritDoc}
     */
    public function getContext(string $specialization)
    {
        return $this->attribute->getContext($specialization);
    }

    public function __toString()
    {
        return $this->getDisplayName();
    }
}
