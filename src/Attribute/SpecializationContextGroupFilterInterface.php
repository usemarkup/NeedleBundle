<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Attribute;

/**
 * Interface for a filter class that can take a group of keyed data and test whether it is a "valid"/acceptable combination.
 */
interface SpecializationContextGroupFilterInterface
{
    /**
     * @param array $attributeData Some data keyed by attribute name, the values being mixed (can depend on the attribute).
     * @return bool
     */
    public function accept(array $attributeData): bool;
}
