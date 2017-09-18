<?php

namespace Markup\NeedleBundle\Filter;

/**
* A filter value representing a range.
*/
class RangeFilterValue implements RangeFilterValueInterface
{
    /**
     * The minimum bound of the range.
     *
     * @var float
     **/
    private $min;

    /**
     * The maximum bound of the range.
     *
     * @var float
     **/
    private $max;

    /**
     * @param float $min
     * @param float $max
     **/
    public function __construct($min, $max)
    {
        $this->min = $min;
        $this->max = $max;
    }

    public function getSearchValue()
    {
        return sprintf('[%s TO %s]', $this->getMin(), $this->getMax());
    }

    public function getSlug()
    {
        return sprintf('%s-%s', $this->getMin(), $this->getMax());
    }

    /**
     * {{@inheritdoc}}
     **/
    public function getMin()
    {
        return $this->min;
    }

    /**
     * {{@inheritdoc}}
     **/
    public function getMax()
    {
        return $this->max;
    }
}
