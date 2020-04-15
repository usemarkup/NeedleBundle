<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Result;

use Markup\NeedleBundle\Attribute\AttributeValueOptionsInterface;
use Markup\NeedleBundle\Collator\CollatorInterface;
use Markup\NeedleBundle\Context\SearchContextInterface;
use Markup\NeedleBundle\Facet\ArbitraryCompositeFacetInterface;
use Markup\NeedleBundle\Facet\CompositeFacetSetIterator;
use Markup\NeedleBundle\Facet\FacetSet;
use Markup\NeedleBundle\Facet\FilterNonUnionValuesFacetSetDecorator;
use Markup\NeedleBundle\Query\SelectQueryInterface;

/**
 * An iterator that goes over a collection of Elasticsearch aggregation results and emits generic facet sets.
 */
class ElasticsearchFacetSetsIterator implements \OuterIterator
{
    /**
     * @var \Iterator
     */
    private $aggregationsIterator;

    /**
     * @var array
     */
    private $facetsKeyedBySearchKey;

    /**
     * @var SearchContextInterface
     */
    private $searchContext;

    /**
     * @var SelectQueryInterface|null
     */
    private $originalQuery;

    /**
     * A sub-iterator of facet sets.
     *
     * @var \Iterator|null
     **/
    private $subIterator;

    public function __construct(
        array $aggregationsData,
        SearchContextInterface $searchContext,
        ?SelectQueryInterface $originalQuery = null
    ) {
        $this->aggregationsIterator = new NonEmptyFacetSetFilterIterator(new \ArrayIterator($aggregationsData));
        $this->searchContext = $searchContext;
        $this->originalQuery = $originalQuery;
        $this->setFacetsKeyedBySearchKey($searchContext);
    }

    private function setFacetsKeyedBySearchKey(SearchContextInterface $context)
    {
        $this->facetsKeyedBySearchKey = [];
        foreach ($context->getFacets() as $facet) {
            $this->facetsKeyedBySearchKey[$facet->getName()] = $facet;
        }
    }

    public function current()
    {
        //deal with possible arbitrary composite facets
        if (null !== $this->subIterator && $this->subIterator->valid()) {
            $facetSet = $this->subIterator->current();
            $this->subIterator->next();
        } elseif (null !== $this->subIterator && !$this->subIterator->valid()) {
            $this->subIterator = null;
            $facetSet = new FacetSet(
                $this->getCurrentFacet(),
                new ElasticsearchFacetSetAdaptingIterator(
                    $this->getInnerIterator()->current()['buckets'] ?? [],
                    $this->getCurrentCollator(),
                    $this->getViewDisplayStrategy()
                )
            );
        } else {
            if ($this->getCurrentFacet() instanceof ArbitraryCompositeFacetInterface) {
                $this->subIterator = new CompositeFacetSetIterator(
                    new FacetSet(
                        $this->getCurrentFacet(),
                        new ElasticsearchFacetSetAdaptingIterator(
                            $this->getInnerIterator()->current()['buckets'] ?? [],
                            $this->getCurrentCollator(),
                            $this->getViewDisplayStrategy()
                        )
                    ),
                    '::'
                );
                $this->subIterator->rewind();
                $facetSet = $this->subIterator->current();
                $this->subIterator->next();
            } else {
                $facetSet = new FacetSet(
                    $this->getCurrentFacet(),
                    new ElasticsearchFacetSetAdaptingIterator(
                        $this->getInnerIterator()->current()['buckets'] ?? [],
                        $this->getCurrentCollator(),
                        $this->getViewDisplayStrategy()
                    )
                );
            }
        }
        //add a decorator to filter out values that don't match combined filter values, in cases where they exist
        $nonCombinedDecorator = new FilterNonUnionValuesFacetSetDecorator($this->originalQuery);
        $facetSet = $nonCombinedDecorator->decorate($facetSet);
        //there may be a configured decorator for this facet set
        $facetSetDecorator = $this->getSearchContext()->getSetDecoratorForFacet($this->getCurrentFacet());
        if ($facetSetDecorator) {
            $facetSet = $facetSetDecorator->decorate($facetSet);
        }

        return $facetSet;
    }

    public function next()
    {
        if (null === $this->subIterator or !$this->subIterator->valid()) {
            $this->getInnerIterator()->next();
        }
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

    public function getInnerIterator()
    {
        return $this->aggregationsIterator;
    }

    private function getCurrentFacet()
    {
        return $this->facetsKeyedBySearchKey[$this->getInnerIterator()->key()];
    }

    /**
     * Gets the collator to use for the current iteration, in order to collate (sort) facet values.
     **/
    private function getCurrentCollator(): ?CollatorInterface
    {
        return $this->getCollatorProvider()->getCollatorForKey($this->getInnerIterator()->key());
    }

    private function getCollatorProvider()
    {
        return $this->getSearchContext()->getFacetCollatorProvider();
    }

    private function getSearchContext(): SearchContextInterface
    {
        return $this->searchContext;
    }

    private function getViewDisplayStrategy(): ?\Closure
    {
        $facet = $this->getCurrentFacet();

        if ($facet instanceof AttributeValueOptionsInterface && $facet->shouldCanonicalizeDisplayValue()) {
            throw new \BadMethodCallException('not implemented');
        }

        return null;
    }
}
