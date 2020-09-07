<?php

namespace Markup\NeedleBundle\Query;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Filter\FilterQueryInterface;
use Markup\NeedleBundle\Sort\DefinedSortOrder;
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

    /**
     * Allows sorting of the results in a specific order. Ususally used in conjunction with a filter against
     * a range of document ids or references (where you know the documents you want to retrieve and the order you want
     * them in
     */
    public function getDefinedSortOrder(): ?DefinedSortOrder;
}
