<?php

namespace Markup\NeedleBundle\Attribute;

/**
 * An attribute implementation that has one or more specializations set on it
 * allowing the search key to be changed by adding a context
 */
interface SpecializedAttributeInterface
{
    /**
     * @return AttributeSpecializationInterface[]
     */
    public function getSpecializations();

    public function setContext(AttributeSpecializationContextInterface $context, string $specialization);

    /**
     * @param string $specialization
     * @return AttributeSpecializationContextInterface
     */
    public function getContext(string $specialization);
}
