<?php

namespace Markup\NeedleBundle\Filter;

/**
* A scalar filter value.
*/
class ScalarFilterValue implements FilterValueInterface
{
    /**
     * A scalar that forms the filter value.
     *
     * @var string|int|bool
     **/
    private $scalar;

    /**
     * @param string|int|bool $scalar
     **/
    public function __construct($scalar)
    {
        $this->scalar = $scalar;
    }

    public function getSearchValue()
    {
        return $this->getScalar();
    }

    public function getSlug()
    {
        return strval($this->getScalar());
    }

    /**
     * Gets the scalar.
     *
     * @return string|int|bool
     **/
    private function getScalar()
    {
        return $this->scalar;
    }

    public function __toString()
    {
        return $this->scalar;
    }
}
