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
     * items_per_page, sorts, sorts_search_term, sorts_non_search_term, boosts, facets, intercepts
     *
     * Values are exactly in the format as the various methods should return.
     */
    private $config;

    /**
     * @param array $config A hash config with keys:
     *
     * items_per_page, base_filter_queries, sorts, sorts_search_term, sorts_non_search_term, boosts, filters, facets, intercepts, should_ignore_current_filters_in_faceting
     */
    public function __construct(array $config = array())
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
     * Gets the default sort stack to use for queries that use a search term, with order indicators given
     *
     * example: [ 'relevance' => 'asc', 'price' => 'desc' ]
     *
     * @return array
     */
    public function getDefaultSortsForSearchTermQuery()
    {
        if (isset($this->config['sorts_search_term'])) {
            return $this->config['sorts_search_term'];
        }
        if (isset($this->config['sorts'])) {
            return $this->config['sorts'];
        }

        return array(ContextConfigurationInterface::SORT_RELEVANCE => ContextConfigurationInterface::ORDER_DESC);
    }

    /**
     * Gets the default sort stack to use for queries that do not use a search term, with order indicators given
     *
     * example: [ 'name' => 'asc', 'price' => 'desc'
     *
     * @return array
     */
    public function getDefaultSortsForNonSearchTermQuery()
    {
        if (isset($this->config['sorts_non_search_term'])) {
            return $this->config['sorts_non_search_term'];
        }
        if (isset($this->config['sorts'])) {
            return $this->config['sorts'];
        }

        return array();
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
     * Gets a list of attributes that can be used for userland filtering.  It only makes sense for any available facets
     * to be taken from this list.
     *
     * example: [ 'gender', 'size', 'on_sale' ]
     *
     * @return array
     */
    public function getFilterableAttributes()
    {
        return $this->config['filters'];
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

    /**
     * @return array
     */
    private function getDefaultConfig()
    {
        return array(
            'items_per_page' => self::DEFAULT_ITEMS_PER_PAGE,
            'base_filter_queries' => array(),
            'boosts' => array(),
            'filters' => array(),
            'facets' => array(),
            'intercepts' => array(),
            'should_ignore_current_filters_in_faceting' => false,
        );
    }
}
