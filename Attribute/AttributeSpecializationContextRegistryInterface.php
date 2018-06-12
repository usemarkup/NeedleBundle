<?php

namespace Markup\NeedleBundle\Attribute;

use Doctrine\Common\Collections\Collection;

/**
 * A registry for all AttributeSpecialization Context
 */
interface AttributeSpecializationContextRegistryInterface
{
    /**
     * Get the currently applicable context for the passed specialization
     *
     * @return AttributeSpecializationContextInterface
     */
    public function getContext(AttributeSpecialization $specialization);

    /**
     * Get all contexts for the passed specialization
     *
     * @return AttributeSpecializationContextInterface[]|Collection
     */
    public function getContexts(AttributeSpecialization $specialization);

    /**
     * Gets a hash of all current SpecializationContexts with currently applicable values
     * @return array
     */
    public function getSpecializationContextHash();
}
