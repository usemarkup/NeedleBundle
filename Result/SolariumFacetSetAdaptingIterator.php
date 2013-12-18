<?php

namespace Markup\NeedleBundle\Result;

use Markup\NeedleBundle\Collator\CollatorInterface;
use Markup\NeedleBundle\Facet\FacetValue;
use Markup\NeedleBundle\Facet\FacetSetIteratorInterface;

/**
* An iterator that can wrap a Solarium facet set and emit generic facet values.
*/
class SolariumFacetSetAdaptingIterator implements \OuterIterator, FacetSetIteratorInterface
{
    /**
     * A Solarium facet value iterator (such as a field).
     *
     * @var \Iterator
     **/
    private $facetValueIterator;

    /**
     * The count of unique facet values within the set.
     *
     * @var int
     **/
    private $count;

    /**
     * @param array|\Traversable $solariumFacetset
     **/
    public function __construct($solariumFacetset, CollatorInterface $collator = null)
    {
        if ($collator) {
            if ($solariumFacetset instanceof \Traversable) {
                $solariumFacetset = iterator_to_array($solariumFacetset);
            }
            $this->count = count($solariumFacetset);
            uksort($solariumFacetset, function ($value1, $value2) use ($collator) { return $collator->compare($value1, $value2); });
            $this->facetValueIterator = new \ArrayIterator($solariumFacetset);

            return;
        }
        if (is_array($solariumFacetset)) {
            $this->count = count($solariumFacetset);
        } else {
            $this->count = count(iterator_to_array($solariumFacetset));
        }
        if ($solariumFacetset instanceof \Iterator) {
            $this->facetValueIterator = $solariumFacetset;
        } elseif ($solariumFacetset instanceof \Traversable) {
            $this->facetValueIterator = new \IteratorIterator($solariumFacetset);
        } elseif (is_array($solariumFacetset)) {
            $this->facetValueIterator = new \ArrayIterator($solariumFacetset);
        } else {
            throw new \InvalidArgumentException(sprintf('%s was passed a solarium_facetset parameter of type "%s", expecting a traversable object or an array.', __METHOD__, gettype($solariumFacetset)));
        }
    }

    public function getInnerIterator()
    {
        return $this->facetValueIterator;
    }

    public function current()
    {
        return new FacetValue($this->getInnerIterator()->key(), $this->getInnerIterator()->current());
    }

    public function next()
    {
        return $this->getInnerIterator()->next();
    }

    public function key()
    {
        return $this->getInnerIterator()->key();
    }

    public function valid()
    {
        return $this->getInnerIterator()->valid();
    }

    public function rewind()
    {
        return $this->getInnerIterator()->rewind();
    }

    public function count()
    {
        return intval($this->count);
    }
}
