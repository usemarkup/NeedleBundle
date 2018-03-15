<?php

namespace Markup\NeedleBundle\Facet;

/**
* A filter iterator that filters facet values by a minimum count value.
*/
class MinCountFacetValueFilterIterator extends \FilterIterator
{
    /**
     * The minimum count an acceptable facet value should have.
     *
     * @var int
     **/
    private $minCount;

    /**
     * @param int       $minimumCount
     * @param \Iterator $facetValueIterator
     **/
    public function __construct($minimumCount, \Iterator $facetValueIterator)
    {
        $this->minCount = intval($minimumCount);
        parent::__construct($facetValueIterator);
    }

    public function accept()
    {
        return count($this->getInnerIterator()->current()) >= $this->getMinCount();
    }

    private function getMinCount()
    {
        return $this->minCount;
    }
}
