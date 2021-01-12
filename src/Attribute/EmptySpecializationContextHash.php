<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Attribute;

final class EmptySpecializationContextHash implements SpecializationContextHashInterface
{
    public function hasContext(string $name): bool
    {
        return false;
    }

    public function getContextData(string $name)
    {
        throw new \InvalidArgumentException();
    }

    public function toArray()
    {
        return [];
    }
}
