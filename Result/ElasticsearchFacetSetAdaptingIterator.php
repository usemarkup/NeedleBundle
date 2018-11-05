<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Result;

use Markup\NeedleBundle\Collator\CollatorInterface;
use Markup\NeedleBundle\Facet\FacetSetIteratorInterface;
use Markup\NeedleBundle\Facet\FacetValue;

class ElasticsearchFacetSetAdaptingIterator implements \OuterIterator, FacetSetIteratorInterface
{
    /**
     * @var \Iterator
     */
    private $facetValueIterator;

    /**
     * @var int
     */
    private $count;

    /**
     * The view display strategy for the facet.
     *
     * @var callable
     */
    private $viewDisplayStrategy;

    public function __construct(
        array $aggregationValues,
        ?CollatorInterface $collator = null,
        ?callable $viewDisplayStrategy = null
    ) {
        if ($collator) {
            usort($aggregationValues, function ($value1, $value2) use ($collator) {
                return $collator->compare($value1['key'], $value2['key']);
            });
        }
        $this->facetValueIterator = new \ArrayIterator($aggregationValues);
        $this->count = count($aggregationValues);
        $this->viewDisplayStrategy = $viewDisplayStrategy ?? function ($value) {
            return $value;
        };
    }

    public function current()
    {
        $current = $this->getInnerIterator()->current();

        return new FacetValue($current['key'], $current['doc_count'], $this->viewDisplayStrategy);
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
        return $this->count;
    }

    public function getInnerIterator()
    {
        return $this->facetValueIterator;
    }
}
