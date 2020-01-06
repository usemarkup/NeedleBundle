<?php

namespace Markup\NeedleBundle\Facet;

/**
* A facet set decorator that filters out values that don't meet a minimum count.
*/
class MinCountFacetSetDecorator extends FacetSetDecorator
{
    use EnsureIteratorTrait;

    /**
     * @var int
     **/
    private $minCount;

    /**
     * @param int $minCount The minimum facet value count that will be applied to this facet set.
     **/
    public function __construct($minCount)
    {
        $this->minCount = $minCount;
    }

    public function getIterator()
    {
        return new MinCountFacetValueFilterIterator(
            $this->getMinCount(),
            $this->ensureIterator(parent::getIterator())
        );
    }

    /**
     * @return int
     **/
    private function getMinCount()
    {
        return $this->minCount;
    }
}
