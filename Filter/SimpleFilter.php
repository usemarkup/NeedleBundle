<?php

namespace Markup\NeedleBundle\Filter;

use Markup\NeedleBundle\Attribute\AttributeInterface;

/**
* A simple named filter.
*/
class SimpleFilter implements AttributeInterface
{
    /**
     * The name for the filter.
     *
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

    public function getName()
    {
        return $this->name;
    }

    public function getDisplayName()
    {
        return ucfirst(str_replace('_', ' ', $this->getName()));
    }

    public function getSearchKey()
    {
        return $this->getName();
    }

    /**
     * Magic toString method.  Returns display name.
     *
     * @return string
     **/
    public function __toString()
    {
        return $this->getDisplayName();
    }
}
