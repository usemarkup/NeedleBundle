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
     * @param FacetSetInterface $facetSet
     * @param string            $valueDelimiter
     **/
    public function __construct(FacetSetInterface $facetSet, $valueDelimiter)
    {
        $this->facetSet = $facetSet;
        $this->valueDelimiter = $valueDelimiter;

        parent::__construct();
    }

    public function current()
    {
        //some quite specific logic here for now...
        $facet = new ArbitraryFacetField(new Filter\SimpleFilter(strval(parent::key())));

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
            [$facetName, $facetValue] = explode($delimiter, $compositeFacetValue->getValue());
            if (!isset($this[$facetName])) {
                $this[$facetName] = [];
            }
            $this[$facetName][] = new FacetValue($facetValue ?? '', count($compositeFacetValue));
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
        return $this->parsed;
    }

    /**
     * @return string
     **/
    private function getValueDelimiter()
    {
        return $this->valueDelimiter;
    }
}
