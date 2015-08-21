<?php

namespace Markup\NeedleBundle\Attribute;

use Doctrine\Common\Collections\Collection;

/**
 * Provides all distinct contexts for a given attribute specialization
 */
interface AttributeSpecializationContextProviderInterface
{
    /**
     * The specialization for which this provides contexts
     * @return AttributeSpecialization
     */
    public function getSpecialization();

    /**
     * @return Collection
     */
    public function getContexts();
}
