<?php

namespace Markup\NeedleBundle\Query;

use Markup\NeedleBundle\Filter\FilterQueryInterface;
use Markup\NeedleBundle\Service\SearchServiceInterface as SearchService;

/**
 * An interface for a select query.
 **/
interface SelectQueryInterface extends SimpleQueryInterface
{
    /**
     * Gets the filters that apply to this query.
     *
     * @return FilterQueryInterface[]
     **/
    public function getFilterQueries();

    /**
     * Gets whether this query contains filter queries.
     *
     * @return bool
     **/
    public function hasFilterQueries();

    /**
     * Gets the page number of results being requested, if specified. Returns null if not specified.
     *
     * @return int|null
     **/
    public function getPageNumber();

    /**
     * Gets the max number of results to return per page. Returns null if not specified.
     * @return integer|null
     */
    public function getMaxPerPage();

    /**
     * Gets the sort collection used by this query.
     *
     * @return \Markup\NeedleBundle\Sort\SortCollectionInterface
     **/
    public function getSortCollection();

    /**
     * Gets whether the query has a (non-empty) sort collection.
     *
     * @return bool
     **/
    public function hasSortCollection();

    /**
     * Gets certain attribute names should not be faceted on for this query.
     *
     * @return string[]
     **/
    public function getFacetNamesToExclude();

    /**
     * Gets the result of the query.
     *
     * @return object (Result class)
     **/
    public function getResult();

    /**
     * @param SearchService $service
     **/
    public function setSearchService(SearchService $service);

    /**
     * Gets the filter query keyed with the passed string. Returns null if no filter query matching the key.
     *
     * @param string $key The search key of the filter query to retrieve
     * @return FilterQueryInterface|null
     */
    public function getFilterQueryWithKey($key);

     /**
     * Determine if the filterquery with key $key, exists in this catalogQuery, and has a value of $value.
     *
     * @param string The search key of the filter query to retrieve
     * @param string The search value of the filter query to retrieve
     * @return boolean
     */
    public function doesValueExistInFilterQueries($key, $value);
}
