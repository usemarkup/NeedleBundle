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
     * @var \Closure
     */
    private $displayStrategy;

    /**
     * @param string $value
     * @param int    $count
     * @param callable $displayStrategy
     **/
    public function __construct($value, $count, $displayStrategy = null)
    {
        $this->value = $value;
        $this->count = $count;
        if (null !== $displayStrategy && !is_callable($displayStrategy)) {
            throw new \InvalidArgumentException('displayStrategy parameter must be callable if set.');
        }
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
