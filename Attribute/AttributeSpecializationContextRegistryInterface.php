<?php

namespace Markup\NeedleBundle\Attribute;

/**
 * A registry for all AttributeSpecialization Context
 */
interface AttributeSpecializationContextRegistryInterface
{
    /**
     * Get the currently applicable context for the passed specialization
     * @return AttributeSpecializationContext
     */
    public function getContext(AttributeSpecialization $specialization);

    /**
     * Get all contexts for the passed specialization
     * @return AttributeSpecializationContext
     */
    public function getContexts(AttributeSpecialization $specialization);

    /**
     * Gets a hash of all current SpecializationContexts with currently applicable values
     * @return array
     */
    public function getSpecializationContextHash();
}
