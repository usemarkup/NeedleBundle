<?php

namespace Markup\NeedleBundle\Attribute;

/**
 * A group of contexts that represent a valid configuration of specialization contexts
 * the currently applied 'context'
 */
class AttributeSpecializationContextGroup
{

    const DISPLAY_SEPARATOR = "/";

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

    /**
     * @return string
     */
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

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return implode(self::DISPLAY_SEPARATOR, array_map(function (AttributeSpecializationContextInterface $s) {
            return $s->getValue();
        }, $this->specializationContexts));
    }

    /**
     * @return array
     */
    public function getSpecializationContexts()
    {
        return $this->specializationContexts;
    }
}
