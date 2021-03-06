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
        if (!is_scalar($scalar)) {
            throw new \InvalidArgumentException('Invalid scalar');
        }

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

    public function getValueType(): string
    {
        return self::TYPE_SIMPLE;
    }

    public function __toString()
    {
        return (string) $this->scalar;
    }
}
