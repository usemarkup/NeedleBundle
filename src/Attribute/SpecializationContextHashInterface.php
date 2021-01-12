<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Attribute;

interface SpecializationContextHashInterface
{
    public function hasContext(string $name): bool;

    /**
     * @param string $name
     * @return mixed
     */
    public function getContextData(string $name);

    public function toArray();
}
