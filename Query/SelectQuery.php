<?php

namespace Markup\NeedleBundle\Query;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Filter\FilterQueryInterface;
use Markup\NeedleBundle\Sort\DefinedSortOrder;
use Markup\NeedleBundle\Sort\SortCollectionInterface;

/**
 * An interface for a select query.
 **/
class SelectQuery implements SelectQueryInterface
{
    /**
     * @var array
     */
    private $fields;

    /**
     * @var int|null
     */
    private $pageNumber;

    /**
     * @var int|null
     */
    private $maxPerPage;

    /**
     * @var array
     */
    private $facetsToExclude;

    /**
     * @var SortCollectionInterface|null
     */
    private $sortCollection;

    /**
     * @var AttributeInterface|null
     */
    private $groupingField;

    /**
     * @var SortCollectionInterface|null
     */
    private $groupingSortCollection;

    /**
     * @var string|null
     */
    private $searchTerm;

    /**
     * @var array|null
     */
    private $facets;

    /**
     * @var array
     */
    private $baseFilterQueries;

    /**
     * @var array
     */
    private $appliedFilterQueries;

    /**
     * @var DefinedSortOrder|null
     */
    private $definedSortOrder;

    public function __construct(
        array $baseFilterQueries,
        array $appliedFilterQueries,
        array $fields,
        ?array $facets = null,
        ?int $pageNumber = null,
        ?int $maxPerPage = null,
        ?string $searchTerm = null,
        array $facetsToExclude = [],
        ?SortCollectionInterface $sortCollection = null,
        ?DefinedSortOrder $definedSortOrder = null,
        ?AttributeInterface $groupingField = null,
        ?SortCollectionInterface $groupingSortCollection = null
    ) {
        $this->facets = $facets;
        $this->fields = $fields;
        $this->pageNumber = $pageNumber;
        $this->maxPerPage = $maxPerPage;
        $this->facetsToExclude = $facetsToExclude;
        $this->sortCollection = $sortCollection;
        $this->groupingField = $groupingField;
        $this->groupingSortCollection = $groupingSortCollection;
        $this->searchTerm = $searchTerm;
        $this->baseFilterQueries = $baseFilterQueries;
        $this->appliedFilterQueries = $appliedFilterQueries;
        $this->definedSortOrder = $definedSortOrder;
    }

    /**
     * @inheritDoc
     */
    public function getFilterQueries(): array
    {
        return array_merge($this->baseFilterQueries, $this->appliedFilterQueries);
    }

    /**
     * @inheritDoc
     */
    public function hasFilterQueries(): bool
    {
        return count($this->getFilterQueries()) > 0;
    }

    /**
     * @inheritDoc
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @inheritDoc
     */
    public function getPageNumber(): ?int
    {
        return $this->pageNumber;
    }

    /**
     * @inheritDoc
     */
    public function getMaxPerPage(): ?int
    {
        return $this->maxPerPage;
    }

    /**
     * @inheritDoc
     */
    public function getSortCollection(): ?SortCollectionInterface
    {
        return $this->sortCollection;
    }

    public function hasSortCollection(): bool
    {
        return $this->sortCollection !== null;
    }

    /**
     * @inheritDoc
     */
    public function getFacetsToExclude(): array
    {
        return $this->facetsToExclude;
    }

    /**
     * @inheritDoc
     */
    public function getFilterQueryWithKey(string $key): ?FilterQueryInterface
    {
        foreach ($this->getFilterQueries() as $filterQuery) {
            if (!$filterQuery instanceof FilterQueryInterface) {
                continue;
            }

            if ($filterQuery->getSearchKey() == $key) {
                return $filterQuery;
            }
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getSpellcheck()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getGroupingField(): ?AttributeInterface
    {
        return $this->groupingField;
    }

    /**
     * @inheritDoc
     */
    public function getGroupingSortCollection(): ?SortCollectionInterface
    {
        return $this->groupingSortCollection;
    }

    /**
     * @inheritDoc
     */
    public function hasSearchTerm(): bool
    {
        return $this->searchTerm !== null;
    }

    /**
     * @inheritDoc
     */
    public function getSearchTerm()
    {
        if (!is_string($this->searchTerm)) {
            return false;
        }

        return $this->searchTerm;
    }

    public function getFacets(): ?array
    {
        return $this->facets;
    }

    public function hasFacets(): bool
    {
        return $this->facets !== null;
    }

    public function getBaseFilterQueries(): array
    {
        return $this->baseFilterQueries;
    }

    public function getAppliedFilterQueries(): array
    {
        return $this->appliedFilterQueries;
    }

    public function getDefinedSortOrder(): ?DefinedSortOrder
    {
        return $this->definedSortOrder;
    }
}
