<?php

namespace Markup\NeedleBundle\Context;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Collator\CollatorProviderInterface;
use Markup\NeedleBundle\Facet\FacetSetDecoratorProviderInterface;
use Markup\NeedleBundle\Facet\SortOrderProviderInterface;
use Markup\NeedleBundle\Intercept\InterceptorInterface;
use Markup\NeedleBundle\Sort\SortCollectionInterface;

/**
 * An interface for contexts for search engines.  This includes any contextual factors that are
 * concerned with the nature of search results, and that are agnostic of the actual search engine implementation.
 **/
interface SearchContextInterface
{
    public function getItemsPerPage(): ?int;

    /**
     * Gets the set of facets to return
     *
     * @return \Markup\NeedleBundle\Attribute\AttributeInterface[]
     **/
    public function getDefaultFacets(): array;

    /**
     * Gets the default filters to be applied to any search with this context.
     *
     * @return \Markup\NeedleBundle\Filter\FilterQueryInterface[]
     **/
    public function getDefaultFilterQueries(): array;

    /**
     * @return \Markup\NeedleBundle\Sort\SortCollectionInterface
     **/
    public function getDefaultSortCollectionForQuery(): ?SortCollectionInterface;

    public function getFacetSetDecoratorProvider(): FacetSetDecoratorProviderInterface;

    /**
     * Gets whether the given facet that is being displayed should ignore any corresponding filter values that are
     * currently selected (true), or whether they should just reflect the returned results (false).
     *
     * @param  AttributeInterface $facet
     * @return bool
     **/
    public function getWhetherFacetIgnoresCurrentFilters(AttributeInterface $facet): bool;

    public function getBoostQueryFields(): array;

    // the collator sorts facets _after_ they come back from solr
    public function getFacetCollatorProvider(): CollatorProviderInterface;

    // facet sort order provider provides an argument that sorts facets _within_ solr (i.e it affects the query)
    public function getFacetSortOrderProvider(): SortOrderProviderInterface;

    public function getInterceptor(): InterceptorInterface;

    public function shouldRequestFacetValueForMissing(): bool;

    public function shouldUseFuzzyMatching(): bool;
}
