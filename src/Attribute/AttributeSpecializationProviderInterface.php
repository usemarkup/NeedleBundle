<?php

namespace Markup\NeedleBundle\Attribute;

interface AttributeSpecializationProviderInterface
{
    /**
     * @return AttributeSpecialization[]
     */
    public function getSpecializations();

    /**
     * @return AttributeSpecialization|null
     */
    public function getSpecialization($name);
}
