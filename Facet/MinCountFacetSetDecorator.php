<?php

namespace Markup\NeedleBundle\Facet;

/**
* A facet set decorator that filters out values that don't meet a minimum count.
*/
class MinCountFacetSetDecorator extends FacetSetDecorator
{
    /**
     * @var int
     **/
    private $minCount;

    /**
     * @param int $min_count The minimum facet value count that will be applied to this facet set.
     **/
    public function __construct($min_count)
    {
        $this->minCount = $min_count;
    }

    public function getIterator()
    {
        return new MinCountFacetValueFilterIterator($this->getMinCount(), parent::getIterator());
    }

    /**
     * @return int
     **/
    private function getMinCount()
    {
        return $this->minCount;
    }
}
