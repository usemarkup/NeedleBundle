<?php

namespace Markup\NeedleBundle\Result;

use Markup\NeedleBundle\Collator\CollatorInterface;
use Markup\NeedleBundle\Facet\FacetSetIteratorInterface;
use Markup\NeedleBundle\Facet\FacetValue;

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
     * @var callable
     */
    private $viewDisplayStrategy;

    public function __construct(iterable $solariumFacetset, ?CollatorInterface $collator = null, ?callable $viewDisplayStrategy = null)
    {
        $this->viewDisplayStrategy = $viewDisplayStrategy ?: function ($value) {
            return $value;
        };
        if ($solariumFacetset instanceof \Traversable) {
            $solariumFacetset = iterator_to_array($solariumFacetset);
        }
        if ($collator) {
            uksort($solariumFacetset, function ($value1, $value2) use ($collator) {
                return $collator->compare($value1, $value2);
            });
        }
        $this->count = count($solariumFacetset);
        $this->facetValueIterator = new \ArrayIterator($solariumFacetset);
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
