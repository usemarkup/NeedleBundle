<?php

namespace Markup\NeedleBundle\Config;

use Markup\NeedleBundle\Filter\FilterQueryInterface;

/**
 * A context configuration object.
 */
class ContextConfiguration implements ContextConfigurationInterface
{
    const DEFAULT_ITEMS_PER_PAGE = 24;

    /**
     * A hash containing all the configuration passed in. Keys are:
     *
     * items_per_page, base_filter_queries, sorts, boosts,
     * facets, intercepts, should_ignore_current_filters_in_faceting, should_use_fuzzy_matching
     *
     * Values are exactly in the format as the various methods should return.
     */
    private $config;

    /**
     * @param array $config A hash config with keys:
     *
     * items_per_page, base_filter_queries, sorts, boosts,
     * facets, intercepts, should_ignore_current_filters_in_faceting
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->getDefaultConfig(), $config);
    }

    /**
     * Gets the default number of items visible per page. 0 (zero) = no page limit.
     *
     * @return int
     */
    public function getDefaultItemsPerPage()
    {
        return intval($this->config['items_per_page']);
    }

    /**
     * Gets the base filter queries that are applied to a query as a baseline.
     *
     * @return FilterQueryInterface[]
     */
    public function getDefaultFilterQueries()
    {
        return $this->config['base_filter_queries'];
    }

    /**
     * Gets the default sort stack to use for queries that do not use a search term, with order indicators given
     *
     * example: [ { 'name' => 'asc' }, { 'price' => 'desc' } ]
     *
     * @return array
     */
    public function getDefaultSortsForNonSearchTermQuery()
    {
        if (isset($this->config['sorts'])) {
            return $this->config['sorts'];
        }

        return [];
    }

    /**
     * Gets the default set of boosts that should be applied to a query on a backend
     *
     * example: [ 'name' => 5, 'category' => 0.8, 'description' => 0.2 ]
     *
     * @return array
     */
    public function getDefaultBoosts()
    {
        return $this->config['boosts'];
    }

    /**
     * Gets the default ordered list of attributes to bring back faceting information for.
     *
     * example: [ 'gender', 'category', 'price', 'color' ]
     *
     * @return array
     */
    public function getDefaultFacetingAttributes()
    {
        return $this->config['facets'];
    }

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
    public function getIntercepts()
    {
        return $this->config['intercepts'];
    }

    /**
     * Gets whether any attributes that are currently being filtered on in a userland query should be ignored in the faceting.
     *
     * @return bool
     */
    public function shouldIgnoreCurrentFilteredAttributesInFaceting()
    {
        return (bool) $this->config['should_ignore_current_filters_in_faceting'];
    }

    public function shouldUseFuzzyMatching()
    {
        return (bool) $this->config['should_use_fuzzy_matching'];
    }

    /**
     * @return array
     */
    private function getDefaultConfig()
    {
        return [
            'items_per_page' => self::DEFAULT_ITEMS_PER_PAGE,
            'base_filter_queries' => [],
            'boosts' => [],
            'facets' => [],
            'intercepts' => [],
            'should_ignore_current_filters_in_faceting' => false,
            'should_use_fuzzy_matching' => false,
        ];
    }
}
