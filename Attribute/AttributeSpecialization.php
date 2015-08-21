<?php

namespace Markup\NeedleBundle\Attribute;

class AttributeSpecialization implements AttributeSpecializationInterface
{
    /**
     * @var string
     **/
    private $name;

    /**
     * @param string $name
     **/
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * {@inherit}
     */
    public function getName()
    {
        return $this->name;
    }
}
