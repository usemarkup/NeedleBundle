<?php

namespace Markup\NeedleBundle\Attribute;

/**
 * A group of specializations that represent a distinct 'specialization combination'
 * that may be applicable to the system, may only (most of the time) have 1 member
 */
class AttributeSpecializationGroup
{
    const DISPLAY_SEPARATOR = "/";

    /**
     * @var array
     */
    private $specializations;

    /**
     * @param array $specializations
     */
    public function __construct(array $specializations)
    {
        $this->specializations = $specializations;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        $json = json_encode($this->specializations);

        if (!$json) {
            throw new \RuntimeException('Unexpected JSON encoding error.');
        }

        return $json;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return implode(self::DISPLAY_SEPARATOR, $this->specializations);
    }

    /**
     * @return array
     */
    public function getSpecializations()
    {
        return $this->specializations;
    }
}
