<?php

namespace Markup\NeedleBundle\Attribute;

use Doctrine\Common\Collections\Collection;

/**
 * A registry for all AttributeSpecialization Context
 */
interface AttributeSpecializationContextRegistryInterface
{
    /**
     * Get all contexts for the passed specialization
     *
     * @param string $specializationName
     * @return AttributeSpecializationContextInterface[]|Collection
     */
    public function getContexts(string $specializationName);

    /**
     * @param string $specializationName
     * @param mixed $data
     * @return AttributeSpecializationContextInterface
     */
    public function getContextForData(string $specializationName, $data): AttributeSpecializationContextInterface;
}
