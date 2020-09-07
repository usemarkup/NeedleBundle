<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Result;

use Markup\NeedleBundle\Attribute\AttributeValueOptionsInterface;
use Markup\NeedleBundle\Collator\CollatorInterface;
use Markup\NeedleBundle\Collator\CollatorProviderInterface;
use Markup\NeedleBundle\Facet\ArbitraryCompositeFacetInterface;
use Markup\NeedleBundle\Facet\CompositeFacetSetIterator;
use Markup\NeedleBundle\Facet\FacetSet;
use Markup\NeedleBundle\Facet\FacetSetDecoratorProviderInterface;

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
     * A sub-iterator of facet sets.
     *
     * @var \Iterator|null
     **/
    private $subIterator;

    /**
     * @var CollatorProviderInterface
     */
    private $collatorProvider;

    /**
     * @var FacetSetDecoratorProviderInterface
     */
    private $facetSetDecoratorProvider;

    public function __construct(
        array $aggregationsData,
        array $facets,
        CollatorProviderInterface $collatorProvider,
        FacetSetDecoratorProviderInterface $facetSetDecoratorProvider
    ) {
        $this->aggregationsIterator = new NonEmptyFacetSetFilterIterator(new \ArrayIterator($aggregationsData));
        $this->setFacetsKeyedBySearchKey($facets);
        $this->collatorProvider = $collatorProvider;
        $this->facetSetDecoratorProvider = $facetSetDecoratorProvider;
    }

    private function setFacetsKeyedBySearchKey(array $facets)
    {
        $this->facetsKeyedBySearchKey = [];
        foreach ($facets as $facet) {
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

        //there may be a configured decorator for this facet set
        $facetSetDecorator = $this->facetSetDecoratorProvider->getDecoratorForFacet($this->getCurrentFacet());

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
        return $this->collatorProvider->getCollatorForKey($this->getInnerIterator()->key());
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
