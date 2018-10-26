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
     * @var callable
     */
    private $displayStrategy;

    /**
     * @param string $value
     * @param int    $count
     * @param callable $displayStrategy
     **/
    public function __construct($value, $count, callable $displayStrategy = null)
    {
        $this->value = $value;
        $this->count = $count;
        $this->displayStrategy = $displayStrategy ?: function ($value) {
            return $value;
        };
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getDisplayValue()
    {
        return call_user_func($this->displayStrategy, $this->getValue());
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
