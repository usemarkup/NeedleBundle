<?php

namespace Markup\NeedleBundle\Facet;

use Markup\NeedleBundle\Filter;

/**
* An iterator that goes over a composite facet set and emits arbitrary sub facet sets.
*/
class CompositeFacetSetIterator extends \ArrayIterator
{
    /**
     * The delimiter between a key and a value within the original facet value.
     *
     * @var string
     **/
    private $valueDelimiter;

    /**
     * The passed-in facet set
     *
     * @var FacetSetInterface
     **/
    private $facetSet;

    /**
     * @var bool
     **/
    private $parsed = false;

    /**
     * @param FacetSetInterface $facet_set
     * @param string            $value_delimiter
     **/
    public function __construct(FacetSetInterface $facet_set, $value_delimiter)
    {
        $this->facetSet = $facet_set;
        $this->valueDelimiter = $value_delimiter;
    }

    public function current()
    {
        //some quite specific logic here for now...
        $facet = new ArbitraryFacetField(new Filter\SimpleFilter(parent::key()));
        $parentCurrent = parent::current();

        return new FacetSet($facet, new FacetSetArrayIterator(parent::current()));
    }

    public function rewind()
    {
        if (!$this->isParsed()) {
            $this->parse();
        }
        parent::rewind();
    }

    private function parse()
    {
        $delimiter = $this->getValueDelimiter();
        foreach ($this->facetSet as $compositeFacetValue) {
            list($facetName, $facetValue) = explode($delimiter, $compositeFacetValue->getValue());
            if (!isset($this[$facetName])) {
                $this[$facetName] = array();
            }
            $this[$facetName][] = new FacetValue($facetValue, count($compositeFacetValue));
        }
        $this->parsed = true;
    }

    /**
     * Gets whether the underlying facet set has yet been parsed.
     *
     * @return bool
     **/
    private function isParsed()
    {
        return (bool) $this->parsed;
    }

    /**
     * @return string
     **/
    private function getValueDelimiter()
    {
        return $this->valueDelimiter;
    }
}
