<?php

namespace Markup\NeedleBundle\Attribute;

/**
 * A group of contexts that represent a valid configuration of specialization contexts
 * the currently applied 'context'
 */
class AttributeSpecializationContextGroup
{

    const DISPLAY_SEPARATOR = " / ";

    /**
     * @var array
     */
    private $specializationContexts;

    /**
     * @param array $specializationContexts An array of contexts keyed by the specialization name
     */
    public function __construct(array $specializationContexts)
    {
        $this->specializationContexts = $specializationContexts;
    }

    public function getKey()
    {
        $key = [];

        foreach ($this->specializationContexts as $specializationName => $specializationContext) {
            $key[$specializationName] = $specializationContext->getValue();
        }

        $json = json_encode($key);
        if (!$json) {
            throw new \RuntimeException('Unexpected JSON encode error.');
        }

        return $json;
    }

    public function getDisplayName(): string
    {
        return implode(self::DISPLAY_SEPARATOR, array_map(function (AttributeSpecializationContextInterface $s) {
            return $s->getValue();
        }, $this->specializationContexts));
    }

    public function toArray(): array
    {
        $hash = [];

        foreach ($this->specializationContexts as $specializationName => $specializationContext) {
            $hash[$specializationName] = $specializationContext->getValue();
        }

        return $hash;
    }

    /**
     * Checks that this group matches multiple specializations
     * @param array $specializationContextHash hash of specialization names and data that this group must match
     * @return bool
     */
    public function hasMatchingSpecializations(array $specializationContextHash): bool
    {
        foreach ($specializationContextHash as $name => $value) {
            if (!$this->hasMatchingSpecialization($name, $value)) {
                return false;
            }
        }

        return true;
    }

    public function hasMatchingSpecialization(string $key, $value): bool
    {
        foreach ($this->getSpecializationContexts() as $name => $context) {
            if ($name === $key && $context->getData() === $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getSpecializationContexts()
    {
        return $this->specializationContexts;
    }
}
