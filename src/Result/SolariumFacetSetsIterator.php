<?php

namespace Markup\NeedleBundle\Result;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Attribute\AttributeValueOptionsInterface;
use Markup\NeedleBundle\Collator\CollatorProviderInterface;
use Markup\NeedleBundle\Facet\ArbitraryCompositeFacetInterface;
use Markup\NeedleBundle\Facet\CompositeFacetSetIterator;
use Markup\NeedleBundle\Facet\FacetSet;
use Markup\NeedleBundle\Facet\FacetSetDecoratorProviderInterface;
use Markup\NeedleBundle\Facet\FacetValueCanonicalizerInterface;
use Solarium\QueryType\Select\Result\FacetSet as SolariumFacetSet;

/**
* An iterator that goes over a collection of Solarium facet sets and emits generic facet sets.
*/
class SolariumFacetSetsIterator implements \OuterIterator
{
    /**
     * @var \Iterator
     **/
    private $solariumFacetSetsIterator;

    /**
     * A collection of existing facets in a search context, keyed by facet search key for easy lookups.
     *
     * @var \Markup\NeedleBundle\Attribute\AttributeInterface[]
     **/
    private $facetsKeyedBySearchKey;

    /**
     * A sub-iterator of facet sets.
     *
     * @var \Iterator|null
     **/
    private $subIterator;

    /**
     * @var FacetValueCanonicalizerInterface
     */
    private $facetValueCanonicalizer;

    /**
     * @var CollatorProviderInterface
     */
    private $collatorProvider;

    /**
     * @var FacetSetDecoratorProviderInterface
     */
    private $facetSetDecoratorProvider;

    public function __construct(
        FacetValueCanonicalizerInterface $facetValueCanonicalizer,
        ?SolariumFacetSet $facetSet,
        array $facets,
        CollatorProviderInterface $collatorProvider,
        FacetSetDecoratorProviderInterface $facetSetDecoratorProvider
    ) {
        if ($facetSet == null) {
            $this->solariumFacetSetsIterator = new NonEmptyFacetSetFilterIterator(
                new \ArrayIterator()
            );
        } else {
            $this->solariumFacetSetsIterator = new NonEmptyFacetSetFilterIterator(
                new \ArrayIterator($this->normalizeFacetData($facetSet))
            );
        }

        $this->setFacetsKeyedBySearchKey($facets);
        $this->facetValueCanonicalizer = $facetValueCanonicalizer;
        $this->collatorProvider = $collatorProvider;
        $this->facetSetDecoratorProvider = $facetSetDecoratorProvider;
    }

    private function setFacetsKeyedBySearchKey(array $facets)
    {
        $this->facetsKeyedBySearchKey = [];
        foreach ($facets as $facet) {
            $this->facetsKeyedBySearchKey[$facet->getSearchKey()] = $facet;
        }
    }

    public function getInnerIterator()
    {
        return $this->solariumFacetSetsIterator;
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
                new SolariumFacetSetAdaptingIterator(
                    $this->getInnerIterator()->current(),
                    $this->getCurrentCollator(),
                    $this->getViewDisplayStrategy()
                )
            );
        } else {
            if ($this->getCurrentFacet() instanceof ArbitraryCompositeFacetInterface) {
                $this->subIterator = new CompositeFacetSetIterator(
                    new FacetSet(
                        $this->getCurrentFacet(),
                        new SolariumFacetSetAdaptingIterator(
                            $this->getInnerIterator()->current(),
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
                    new SolariumFacetSetAdaptingIterator(
                        $this->getInnerIterator()->current(),
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

    private function getCurrentFacet()
    {
        return $this->facetsKeyedBySearchKey[$this->getInnerIterator()->key()];
    }

    /**
     * Gets the collator to use for the current iteration, in order to collate (sort) facet values.
     *
     * @return \Markup\NeedleBundle\Collator\CollatorInterface|null
     **/
    private function getCurrentCollator()
    {
        return $this->collatorProvider->getCollatorForKey($this->getInnerIterator()->key());
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

    /**
     * Normalizes incoming facet data.  Combines facets with exclude_* and include_* forms.
     *
     * @param SolariumFacetSet $facetSet
     *
     * @return array
     **/
    private function normalizeFacetData(SolariumFacetSet $facetSet)
    {
        $facets = iterator_to_array($facetSet);
        $normalizedFacets = [];
        foreach ($facets as $name => $facet) {
            $name = str_replace('facet_', '', $name);

            $normalizedFacets[$name] = $facet;
        }

        return $normalizedFacets;
    }

    /**
     * @return \Closure|null
     */
    private function getViewDisplayStrategy()
    {
        $facet = $this->getCurrentFacet();

        if ($facet instanceof AttributeValueOptionsInterface && !$facet->shouldCanonicalizeDisplayValue()) {
            return null;
        }

        if (!$facet instanceof AttributeInterface) {
            return null;
        }

        return function (string $value) use ($facet): string {
            return $this->facetValueCanonicalizer->canonicalizeForFacet($value, $facet);
        };
    }
}
