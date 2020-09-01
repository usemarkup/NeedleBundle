<?php

namespace Markup\NeedleBundle\Query;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Filter\FilterQueryInterface;
use Markup\NeedleBundle\Sort\SortCollectionInterface;
use Markup\NeedleBundle\Spellcheck\SpellcheckInterface;

/**
 * An interface for a select query.
 **/
interface SelectQueryInterface extends SimpleQueryInterface
{
    // combined base filter and applied filter queries
    public function getFilterQueries(): array;

    // base filters are those added in such a way that the user manipulating filters cannot get rid of them
    public function getBaseFilterQueries(): array;

    // applied filters are those chose by the user
    public function getAppliedFilterQueries(): array;

    public function hasFilterQueries(): bool;

    public function getFacets(): ?array;

    public function hasFacets(): bool;

    public function getFields(): array;

    public function getPageNumber(): ?int;

    public function getMaxPerPage(): ?int;

    public function getSortCollection(): ?SortCollectionInterface;

    public function hasSortCollection(): bool;

    /**
     * Gets certain attribute names should not be faceted on for this query.
     *
     * @return array|AttributeInterface[]
     * @deprecated
     **/
    public function getFacetsToExclude(): array;

    /**
     * Gets the filter query keyed with the passed string. Returns null if no filter query matching the key.
     *
     * @param string $key The search key of the filter query to retrieve
     * @return FilterQueryInterface|null
     */
    public function getFilterQueryWithKey(string $key): ?FilterQueryInterface;

     /**
     * Determine if the filter query with key $key, exists in this select query, and has a value of $value.
     *
     * @param string $key   The search key of the filter query to retrieve
     * @param string $value search value of the filter query to retrieve
     * @return bool
     */
    public function doesValueExistInFilterQueries(string $key, $value);

    /**
     * Gets whether consuming code should interpret this query as a text search. For example, even if there is a search term
     * in the query, we may not wish to treat the query as a text search in a particular context (and so possibly apply
     * different default sorts etc)
     *
     * @return bool
     * @deprecated
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
     * @return AttributeInterface|null
     */
    public function getGroupingField(): ?AttributeInterface;

    /**
     * Gets the sort collection used to sort the group internally
     *
     * @return SortCollectionInterface|null
     **/
    public function getGroupingSortCollection(): ?SortCollectionInterface;
}
