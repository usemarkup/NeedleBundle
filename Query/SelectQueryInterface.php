<?php

namespace Markup\NeedleBundle\Query;

use GuzzleHttp\Promise\PromiseInterface;
use Markup\NeedleBundle\Filter\FilterQueryInterface;
use Markup\NeedleBundle\Service\SearchServiceInterface as SearchService;
use Markup\NeedleBundle\Spellcheck\SpellcheckInterface;

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
     * Gets a list of explicit field names on the backend to return. If empty, will not specify fields on any backend.
     *
     * @return string[]
     */
    public function getFields();

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
     * Gets the result of the query as a promise.
     *
     * @return PromiseInterface
     */
    public function getResultAsync();

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
     * Determine if the filter query with key $key, exists in this select query, and has a value of $value.
     *
     * @param string $key   The search key of the filter query to retrieve
     * @param string $value search value of the filter query to retrieve
     * @return bool
     */
    public function doesValueExistInFilterQueries($key, $value);

    /**
     * Gets whether consuming code should interpret this query as a text search. For example, even if there is a search term
     * in the query, we may not wish to treat the query as a text search in a particular context (and so possibly apply
     * different default sorts etc)
     *
     * @return bool
     */
    public function shouldTreatAsTextSearch();

    /**
     * Gets a spellcheck on the query, or null if none set.
     *
     * @return SpellcheckInterface|null
     */
    public function getSpellcheck();

    /**
     * Gets a grouping field, or null if none set.
     *
     * @return string|null
     */
    public function getGroupingField();

    /**
     * Gets the sort collection used to sort the group internally
     *
     * @return \Markup\NeedleBundle\Sort\SortCollectionInterface|null
     **/
    public function getGroupingSortCollection();
}
