<?php

namespace Markup\NeedleBundle\Query;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Boost\BoostQueryField;
use Markup\NeedleBundle\Collator\CollatorProviderInterface;
use Markup\NeedleBundle\Facet\FacetSetDecoratorProviderInterface;
use Markup\NeedleBundle\Filter\FilterQueryInterface;
use Markup\NeedleBundle\Sort\DefinedSortOrder;
use Markup\NeedleBundle\Sort\SortCollectionInterface;

/**
 * A combination of SelectQuery and SearchContext. This should be used immediately prior to query execution in
 * order to allow decoration of the query immediately before execution.
 *
 * This structure represents a more accurate version of the actual query that is executed against the search backend
 * than the Query alone.
 */
interface ResolvedSelectQueryInterface
{
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
     * @return array|FilterQueryInterface[]
     */
    public function getContextFilterQueries(): array;

    /**
     * @return array|FilterQueryInterface[]
     */
    public function getBaseFilterQueries(): array;

    /**
     * @return array|FilterQueryInterface[]
     */
    public function getBaseAndContextFilterQueries(): array;

    /**
     * @return array|FilterQueryInterface[]
     */
    public function getAppliedFilterQueries(): array;

    /**
     * @return array|AttributeInterface[]
     */
    public function getFields(): array;

    /**
     * Provides a hash of field names (that were requested)
     * to attribute search keys (that exist on the document)
     *
     * This is needed to be able to read specialized attributes where the keys differ from those requested by the user
     */
    public function getMappingHashForFields(): array;

    public function getPageNumber(): int;

    public function getMaxPerPage(): int;

    public function hasSearchTerm(): bool;

    public function getSearchTerm(): string;

    public function getSortCollection(): ?SortCollectionInterface;
    
    public function getDefinedSortOrder(): ?DefinedSortOrder;

    public function getFacets(): array;

    /**
     * Gets the sort order to use for sorting facet values in a search engine, given a particular facet.
     *
     * @param  AttributeInterface $facet
     * @return mixed
     **/
    public function getSortOrderForFacet(AttributeInterface $facet);


    /**
     * Gets whether the given facet that is being displayed should ignore any corresponding filter values that
     * are currently selected (true), or whether they should just reflect the returned results (false).
     *
     * In effect when this is 'true' then selecting a facet value (e.g green) would
     * allow another facet value to be selected (e.g yellow)
     *
     * If set to 'false' then selecting a facet value (e.g green) would prevent another color being selected and the
     * user would need to 'clear all' facets in order to reselect
     *
     * @return bool
     **/
    public function getWhetherFacetIgnoresCurrentFilters(AttributeInterface $facet);

    /**
     * Gets whether a query should request facet values for missing (i.e. no match on that facet)
     * For example you have a facet called 'fit'. Setting this to true would create a new facet value of 'none' that
     * matched any documents where the 'fit' value was null
     *
     * @return bool
     **/
    public function shouldRequestFacetValueForMissing();

    /**
     * Gets the set of boost query fields that should be defined against this query.
     *
     * @return BoostQueryField[]
     **/
    public function getBoostQueryFields(): array;

    /**
     * Gets the sort collection used to sort the group internally
     *
     * @return \Markup\NeedleBundle\Sort\SortCollectionInterface|null
     **/
    public function getGroupingSortCollection();

    /**
     * Gets whether this query should be executed using fuzzy matching functionality, if available within a backend.
     */
    public function shouldUseFuzzyMatching(): bool;

    public function getFacetCollatorProvider(): CollatorProviderInterface;
    public function getFacetSetDecoratorProvider(): FacetSetDecoratorProviderInterface;

    public function getGroupingField(): ?AttributeInterface;
}
