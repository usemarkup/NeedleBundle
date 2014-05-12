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
     * The view display strategy for the facet.
     *
     * @var \Closure
     */
    private $viewDisplayStrategy;

    /**
     * @param array|\Traversable $solariumFacetset
     * @param CollatorInterface|null $collator
     * @param callable $viewDisplayStrategy
     **/
    public function __construct($solariumFacetset, CollatorInterface $collator = null, $viewDisplayStrategy = null)
    {
        if (null !== $viewDisplayStrategy && !is_callable($viewDisplayStrategy)) {
            throw new \InvalidArgumentException('viewDisplayStrategy parameter must be callable if it is set.');
        }
        $this->viewDisplayStrategy = $viewDisplayStrategy ?: function ($value) {
            return $value;
        };
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
        return new FacetValue($this->getInnerIterator()->key(), $this->getInnerIterator()->current(), $this->viewDisplayStrategy);
    }

    public function next()
    {
        $this->getInnerIterator()->next();
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
        $this->getInnerIterator()->rewind();
    }

    public function count()
    {
        return intval($this->count);
    }
}
