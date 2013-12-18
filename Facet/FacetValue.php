<?php

namespace Markup\NeedleBundle\Facet;

class FacetValue implements FacetValueInterface
{
    /**
     * @var string
     **/
    private $value;

    /**
     * @var int
     **/
    private $count;

    /**
     * @param string $value
     * @param int    $count
     **/
    public function __construct($value, $count)
    {
        $this->value = $value;
        $this->count = $count;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getDisplayValue()
    {
        return $this->getValue();
    }

    public function count()
    {
        return $this->count;
    }

    public function __toString()
    {
        return strval($this->getValue());
    }
}
