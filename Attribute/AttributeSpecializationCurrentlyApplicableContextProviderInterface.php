<?php

namespace Markup\NeedleBundle\Attribute;

/**
 * Provides the single applicable context for a given specialization
 */
interface AttributeSpecializationCurrentlyApplicableContextProviderInterface
{
    /**
     * The specialization for which this provides a context
     * @return AttributeSpecialization
     */
    public function getSpecialization();

    /**
     * @return AttributeSpecializationContext
     */
    public function getContext();
}
