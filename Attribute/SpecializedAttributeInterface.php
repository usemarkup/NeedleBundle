<?php

namespace Markup\NeedleBundle\Attribute;

/**
 * An attribute implementation that has a specialization set on it
 * allowing the search key to be changed by adding a context
 */
interface SpecializedAttributeInterface
{
    /**
     * @return AttributeSpecialization
     */
    public function getSpecialization();

    /**
     * @param AttributeSpecializationContextInterface
     */
    public function setContext(AttributeSpecializationContextInterface $context);

    /**
     * @return AttributeSpecializationContextInterface
     */
    public function getContext();
}
