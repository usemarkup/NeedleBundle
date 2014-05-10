<?php

namespace Markup\NeedleBundle\Result;

use Markup\NeedleBundle\Context\SearchContextInterface as SearchContext;
use Markup\NeedleBundle\Facet\ArbitraryCompositeFacetInterface;
use Markup\NeedleBundle\Facet\CompositeFacetSetIterator;
use Markup\NeedleBundle\Facet\FacetSet;
use Markup\NeedleBundle\Facet\FilterNonUnionValuesFacetSetDecorator;
use Markup\NeedleBundle\Query\SelectQueryInterface;
use Solarium\QueryType\Select\Result\FacetSet as SolariumFacetSet;
use Solarium\QueryType\Select\Result\Facet\Field as SolariumFacetField;

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
     * @var SearchContext
     **/
    private $searchContext;

    /**
     * @var SelectQueryInterface
     */
    private $originalQuery;

    /**
     * A sub-iterator of facet sets.
     *
     * @var \Iterator
     **/
    private $subIterator = null;

    /**
     * @param SolariumFacetSet             $facetSet
     * @param SearchContext                $searchContext
     **/
    public function __construct(SolariumFacetSet $facetSet, SearchContext $searchContext, SelectQueryInterface $originalQuery = null)
    {
        $this->solariumFacetSetsIterator = new NonEmptyFacetSetFilterIterator(new \ArrayIterator($this->normalizeFacetData($facetSet)));
        $this->searchContext = $searchContext;
        $this->originalQuery = $originalQuery;
        $this->setFacetsKeyedBySearchKey($searchContext);
    }

    /**
     * @param SearchContext $context
     **/
    private function setFacetsKeyedBySearchKey(SearchContext $context)
    {
        $this->facetsKeyedBySearchKey = array();
        foreach ($context->getFacets() as $facet) {
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
            $facetSet = new FacetSet($this->getCurrentFacet(), new SolariumFacetSetAdaptingIterator($this->getInnerIterator()->current(), $this->getCurrentCollator()));
        } else {
            if ($this->getCurrentFacet() instanceof ArbitraryCompositeFacetInterface) {
                $this->subIterator = new CompositeFacetSetIterator(new FacetSet($this->getCurrentFacet(), new SolariumFacetSetAdaptingIterator($this->getInnerIterator()->current(), $this->getCurrentCollator())), '::');
                $this->subIterator->rewind();
                $facetSet = $this->subIterator->current();
                $this->subIterator->next();
            } else {
                $facetSet = new FacetSet($this->getCurrentFacet(), new SolariumFacetSetAdaptingIterator($this->getInnerIterator()->current(), $this->getCurrentCollator()));
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
        return $this->getCollatorProvider()->getCollatorForKey($this->getInnerIterator()->key());
    }

    private function getCollatorProvider()
    {
        return $this->getSearchContext()->getFacetCollatorProvider();
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
     * @return \Iterator
     **/
    private function normalizeFacetData(SolariumFacetSet $facetSet)
    {
        $facets = iterator_to_array($facetSet);
        $normalizedFacets = array();
        foreach ($facets as $name => $facet) {
            //check for includes and excludes
            if (substr($name, 0, 7) == 'include' && isset($facets['exclude_' . substr($name, 8)])) {
                $normalizedFacets[substr($name, 8)] = $this->combineFacetValueFieldWithCountField($facets['exclude_' . substr($name, 8)], $facet);
                continue;
            }
            if (substr($name, 0, 7) == 'exclude' && isset($facets['include_' . substr($name, 8)])) {
                continue;
            }
            //just copy down (default action)
            $normalizedFacets[$name] = $facet;
        }

        return $normalizedFacets;
    }

    /**
     * @param SolariumFacetField $valueField
     * @param SolariumFacetField $countField
     *
     * @return array
     **/
    private function combineFacetValueFieldWithCountField(SolariumFacetField $valueField, SolariumFacetField $countField)
    {
        $facetValues = array();
        $counts = iterator_to_array($countField);
        foreach ($valueField as $value => $count) {
            if (!isset($counts[$value])) {
                $facetValues[$value] = 0;
                continue;
            }
            $facetValues[$value] = $counts[$value];
        }

        return $facetValues;
    }

    /**
     * @return SearchContext
     **/
    private function getSearchContext()
    {
        return $this->searchContext;
    }
}
