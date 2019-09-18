<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Attribute;

class SpecializationContextHash implements SpecializationContextHashInterface
{
    /**
     * @var array
     */
    private $map;

    public function __construct(array $map)
    {
        $this->map = $map;
    }

    public function hasContext(string $name): bool
    {
        return isset($this->map[$name]);
    }

    /**
     * @inheritDoc
     */
    public function getContextData(string $name)
    {
        if (!isset($this->map[$name])) {
            throw new \Exception('fucked it');
        }

        return $this->map[$name];
    }

    public function toArray()
    {
        return $this->map;
    }
}
