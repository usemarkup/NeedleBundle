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
     * @param int       $minimum_count
     * @param \Iterator $facet_value_iterator
     **/
    public function __construct($minimum_count, \Iterator $facet_value_iterator)
    {
        $this->minCount = intval($minimum_count);
        parent::__construct($facet_value_iterator);
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
