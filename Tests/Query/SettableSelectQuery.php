<?php

namespace Markup\NeedleBundle\Tests\Query;

use Markup\NeedleBundle\Filter\FilterQueryInterface;
use Markup\NeedleBundle\Query\FallbackAsyncQueryTrait;
use Markup\NeedleBundle\Query\SelectQueryInterface;
use Markup\NeedleBundle\Service\SearchServiceInterface as SearchService;
use Markup\NeedleBundle\Sort\SortCollection;
use Markup\NeedleBundle\Spellcheck\SpellcheckInterface;

class SettableSelectQuery implements SelectQueryInterface
{
    use FallbackAsyncQueryTrait;

    /**
     * @var string
     */
    private $searchTerm;

    /**
     * @param null $searchTerm
     */
    public function __construct($searchTerm = null)
    {
        $this->searchTerm = $searchTerm;
    }

    /**
     * Gets whether there is a search term associated with this query (i.e. typically a human-entered text search).
     *
     * @return bool
     **/
    public function hasSearchTerm()
    {
        return null !== $this->searchTerm;
    }

    /**
     * Gets the search term being used in this query.  Returns false if not specified.
     *
     * @return string|bool
     **/
    public function getSearchTerm()
    {
        return $this->searchTerm;
    }

    /**
     * @param string $term
     */
    public function setSearchTerm($term)
    {
        $this->searchTerm = $term;

        return $this;
    }

    /**
     * Gets the filters that apply to this query.
     *
     * @return FilterQueryInterface[]
     **/
    public function getFilterQueries()
    {
        return [];
    }

    /**
     * Gets whether this query contains filter queries.
     *
     * @return bool
     **/
    public function hasFilterQueries()
    {
        return [];
    }

    /**
     * Gets a list of explicit field names on the backend to return. If empty, will not specify fields on any backend.
     *
     * @return string[]
     */
    public function getFields()
    {
        return [];
    }

    /**
     * Gets the page number of results being requested, if specified. Returns null if not specified.
     *
     * @return int|null
     **/
    public function getPageNumber()
    {
        return null;
    }

    /**
     * Gets the max number of results to return per page. Returns null if not specified.
     * @return integer|null
     */
    public function getMaxPerPage()
    {
        return null;
    }

    /**
     * Gets the sort collection used by this query.
     *
     * @return \Markup\NeedleBundle\Sort\SortCollectionInterface
     **/
    public function getSortCollection()
    {
        return new SortCollection();
    }

    /**
     * Gets whether the query has a (non-empty) sort collection.
     *
     * @return bool
     **/
    public function hasSortCollection()
    {
        return false;
    }

    /**
     * Gets certain attribute names should not be faceted on for this query.
     *
     * @return string[]
     **/
    public function getFacetNamesToExclude()
    {
        return [];
    }

    /**
     * Gets the result of the query.
     *
     * @return object (Result class)
     **/
    public function getResult()
    {
        return null;
    }

    /**
     * @param SearchService $service
     **/
    public function setSearchService(SearchService $service)
    {
        // do nothing
    }

    /**
     * Gets the filter query keyed with the passed string. Returns null if no filter query matching the key.
     *
     * @param string $key The search key of the filter query to retrieve
     * @return FilterQueryInterface|null
     */
    public function getFilterQueryWithKey($key)
    {
        return null;
    }

    /**
     * Determine if the filter query with key $key, exists in this select query, and has a value of $value.
     *
     * @param string $key   The search key of the filter query to retrieve
     * @param string $value The search value of the filter query to retrieve
     * @return bool
     */
    public function doesValueExistInFilterQueries($key, $value)
    {
        return false;
    }

    /**
     * Gets whether consuming code should interpret this query as a text search. For example, even if there is a search term
     * in the query, we may not wish to treat the query as a text search in a particular context (and so possibly apply
     * different default sorts etc)
     *
     * @return bool
     */
    public function shouldTreatAsTextSearch()
    {
        return false;
    }

    /**
     * Gets a spellcheck on the query, or null if none set.
     *
     * @return SpellcheckInterface|null
     */
    public function getSpellcheck()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupingField()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupingSortCollection()
    {
        return null;
    }

}
