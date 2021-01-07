<?php

namespace Markup\NeedleBundle\Config;

/**
 * Interface for a configuration for a search context, giving defined simple data structures.
 */
interface ContextConfigurationInterface
{
    const SORT_RELEVANCE = 'relevance';
    const ORDER_ASC = 'asc';
    const ORDER_DESC = 'desc';

    /**
     * Gets the default number of items visible per page. 0 (zero) = no page limit.
     *
     * @return int
     */
    public function getDefaultItemsPerPage();

    /**
     * Gets the base filter queries that are applied to a query as a baseline.
     *
     * example: { 'active' => true, 'in_stock' => true }
     *
     * @return array
     */
    public function getDefaultFilterQueries();

    /**
     * Gets the default sort stack to use for queries that do not use a search term, with order indicators given
     *
     * example: [ { 'name' => 'asc' }, { 'price' => 'desc' } ]
     *
     * @return array
     */
    public function getDefaultSortsForNonSearchTermQuery();

    /**
     * Gets the default set of boosts that should be applied to a query on a backend
     *
     * example: { 'name' => 5, 'category' => 0.8, 'description' => 0.2 }
     *
     * @return array
     */
    public function getDefaultBoosts();

    /**
     * Gets the default ordered list of attributes to bring back faceting information for.
     *
     * example: [ 'gender', 'category', 'price', 'color' ]
     *
     * @return array
     */
    public function getDefaultFacetingAttributes();

    /**
     * Gets a config hash for intercepts that apply in this context.
     *
     * example: [
     *     'sale' => [ 'terms' => [ 'sale' ], 'type' => 'route', 'route' => 'shop_sale', 'route_params' => [] ],
     *     '3xl' => [ 'terms' => [ 'XXXL', '3XL' ], 'type' => 'search', 'filters' => [ 'size' => 'XXXL' ] ],
     * ]
     *
     * @return array
     */
    public function getIntercepts();

    /**
     * Gets whether any attributes that are currently being filtered on in a userland query should be ignored in the faceting.
     *
     * @return bool
     */
    public function shouldIgnoreCurrentFilteredAttributesInFaceting();

    /**
     * Gets whether this context configuration suggests that queries should use fuzzy matching on search terms where applicable.
     *
     * @return bool
     */
    public function shouldUseFuzzyMatching();
}
