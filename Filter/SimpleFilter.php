<?php

namespace Markup\NeedleBundle\Filter;

/**
* A simple named filter.
*/
class SimpleFilter implements FilterInterface
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
}
