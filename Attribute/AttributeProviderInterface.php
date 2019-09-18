<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Attribute;

/**
 * Interface for an attribute provider that can provide an attribute when given a name or search key
 */
interface AttributeProviderInterface
{
    public function getAttributeByName(
        string $name,
        SpecializationContextHashInterface $contextHash
    ): ?AttributeInterface;
}
