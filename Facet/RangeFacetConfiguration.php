<?php

namespace Markup\NeedleBundle\Facet;

/**
* A configuration for a range facet.
*/
class RangeFacetConfiguration implements RangeFacetConfigurationInterface
{
    /**
     * @var float
     **/
    private $gap;

    /**
     * @var float
     **/
    private $start;

    /**
     * @var float
     **/
    private $end;

    /**
     * @param float $gap
     * @param float $start
     * @param float $end
     **/
    public function __construct($gap, $start, $end)
    {
        $this->gap = $gap;
        $this->start = $start;
        $this->end = $end;
    }

    public function getStart()
    {
        return $this->start;
    }

    public function getEnd()
    {
        return $this->end;
    }

    public function getGap()
    {
        return $this->gap;
    }
}
