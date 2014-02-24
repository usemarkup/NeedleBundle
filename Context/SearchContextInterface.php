<?php

namespace Markup\NeedleBundle\Context;

use Markup\NeedleBundle\Facet\FacetInterface;
use Markup\NeedleBundle\Query\SelectQueryInterface;

/**
 * An interface for contexts for search engines.  This includes any contextual factors that are concerned with the nature of search results, and that are agnostic of the actual search engine implementation.
 **/
interface SearchContextInterface
{
    /**
     * Gets the number of items that should be shown per page in a paged view.  Returns null if no constraint on numbers exists.
     *
     * @return int|null
     **/
    public function getItemsPerPage();

    /**
     * Gets the set of facets to apply to the search.
     *
     * @return \Markup\NeedleBundle\Facet\FacetInterface[]
     **/
    public function getFacets();

    /**
     * Gets the default filters to be applied to any search with this context.
     *
     * @return \Markup\NeedleBundle\Filter\FilterQueryInterface[]
     **/
    public function getDefaultFilterQueries();

    /**
     * Gets the default sort collection to be applied to a query using this context.
     *
     * @param SelectQueryInterface $query
     *
     * @return \Markup\NeedleBundle\Sort\SortCollectionInterface
     **/
    public function getDefaultSortCollectionForQuery(SelectQueryInterface $query);

    /**
     * Gets the facet set decorator to apply for a specific facet. (This can determine how a facet set renders.) Returns false if no decoration to be applied.
     *
     * @param  FacetInterface                                         $facet
     * @return \Markup\NeedleBundle\Facet\FacetSetInterface|bool
     **/
    public function getSetDecoratorForFacet(FacetInterface $facet);

    /**
     * Gets whether the given facet that is being displayed should ignore any corresponding filter values that are currently selected (true), or whether they should just reflect the returned results (false).
     *
     * @return bool
     **/
    public function getWhetherFacetIgnoresCurrentFilters(FacetInterface $facet);

    /**
     * Gets a list of available filter names that a userland query using this context can filter on.
     *
     * @return array
     **/
    public function getAvailableFilterNames();

    /**
     * Gets the set of boost query fields that should be defined against this query.
     *
     * @return array
     **/
    public function getBoostQueryFields();

    /**
     * Gets a provider object for collator (sorter) objects that can collate facet values.  May return null if no userland sorting of values should be done.
     *
     * @return \Markup\NeedleBundle\Provider\CollatorProviderInterface
     **/
    public function getFacetCollatorProvider();

    /**
     * Gets a signifier for the sort order to use for sorting of facet values within the search engine itself (i.e. not within the web application).
     *
     * @return \Markup\NeedleBundle\Facet\SortOrderProviderInterface
     **/
    public function getFacetSortOrderProvider();

    /**
     * Gets an interceptor object that can intercept a lookup on a backend and provide redirects to specific places.
     *
     * @return \Markup\NeedleBundle\Intercept\InterceptorInterface
     **/
    public function getInterceptor();
}
