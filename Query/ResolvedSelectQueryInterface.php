<?php

namespace Markup\NeedleBundle\Query;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Boost\BoostQueryField;
use Markup\NeedleBundle\Collator\CollatorProviderInterface;
use Markup\NeedleBundle\Context\SearchContextInterface;
use Markup\NeedleBundle\Filter\FilterQueryInterface;
use Markup\NeedleBundle\Sort\SortCollectionInterface;

/**
 * A combination of SelectQuery and SearchContext. This should be used immediately prior to query execution in
 * order to allow decoration of the query immediatley before execution.
 *
 * This structure represents a more accurate version of the actual query that is executed against the search backend
 * than the Query alone
 */
interface ResolvedSelectQueryInterface
{
    public function getSearchContext(): SearchContextInterface;

    public function getFilterQueryWithKey(string $key): ?FilterQueryInterface;

    /**
     * @return array|AttributeInterface[]
     */
    public function getFacetsToExclude(): array;

    /**
     * @return array|FilterQueryInterface[]
     */
    public function getFilterQueries(): array;

    /**
     * @return array|AttributeInterface[]
     */
    public function getFields(): array;

    /**
     * Gets the page number of results being requested, if specified. Returns null if not specified.
     *
     * @return int
     **/
    public function getPageNumber(): int;

    public function doesValueExistInFilterQueries($key, $value);

    /**
     * Gets the max number of results to return per page. Returns null if not specified.
     * @return int
     */
    public function getMaxPerPage(): int;

    public function hasSearchTerm(): bool;

    public function getSearchTerm(): string;

    public function getSortCollection(): SortCollectionInterface;

    /**
     * Gets the set of facets that should be requested with this context.
     *
     * @return \Markup\NeedleBundle\Attribute\AttributeInterface[]
     **/
    public function getFacets();

    /**
     * Gets whether a query should request facet values for missing (i.e. no match on that facet)
     *
     * @return bool
     **/
    public function shouldRequestFacetValueForMissing();

    /**
     * Gets the sort order to use for sorting facet values in a search engine, given a particular facet.
     *
     * @param  AttributeInterface $facet
     * @return mixed
     **/
    public function getSortOrderForFacet(AttributeInterface $facet);

    /**
     * Gets whether the given facet that is being displayed should ignore any corresponding filter values that are currently selected (true), or whether they should just reflect the returned results (false).
     *
     * @return bool
     **/
    public function getWhetherFacetIgnoresCurrentFilters(AttributeInterface $facet);

    /**
     * Gets the set of boost query fields that should be defined against this query.
     *
     * @return BoostQueryField[]
     **/
    public function getBoostQueryFields();

    /**
     * Determines whether we are using the facet values for an underlying "base" (recorded) query
     *
     * @return bool
     **/
    public function shouldUseFacetValuesForRecordedQuery();

    /**
     * Gets the original select query prior to resolution, for read-only access.
     *
     * @return SelectQueryInterface
     */
    public function getOriginalSelectQuery();

    /**
     * If the internal query is an instance of RecordableSelectQueryInterface and has a record, returns SelectQueryInterface
     * otherwise null
     *
     * @return SelectQueryInterface|null
     **/
    public function getRecord();

    /**
     * Gets the sort collection used to sort the group internally
     *
     * @return \Markup\NeedleBundle\Sort\SortCollectionInterface|null
     **/
    public function getGroupingSortCollection();

    /**
     * Gets whether this query should be executed using fuzzy matching functionality, if available within a backend.
     *
     * @return bool
     */
    public function shouldUseFuzzyMatching();

    public function getFacetCollatorProvider(): CollatorProviderInterface;

    public function getGroupingField(): ?AttributeInterface;

    public function shouldTreatAsTextSearch(): bool;
}
