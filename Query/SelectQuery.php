<?php

namespace Markup\NeedleBundle\Query;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Filter\CombinedFilterValueInterface;
use Markup\NeedleBundle\Filter\FilterQueryInterface;
use Markup\NeedleBundle\Filter\FilterValueInterface;
use Markup\NeedleBundle\Sort\EmptySortCollection;
use Markup\NeedleBundle\Sort\SortCollectionInterface;

/**
 * An interface for a select query.
 **/
class SelectQuery implements SelectQueryInterface
{
    /**
     * @var array
     */
    private $filterQueries;

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
     * @var bool
     */
    private $shouldTreatAsTextSearch;

    /**
     * @var string|null
     */
    private $searchTerm;

    public function __construct(
        array $filterQueries,
        array $fields,
        ?int $pageNumber = null,
        ?int $maxPerPage = null,
        ?string $searchTerm = null,
        array $facetsToExclude = [],
        ?SortCollectionInterface $sortCollection = null,
        ?AttributeInterface $groupingField = null,
        ?SortCollectionInterface $groupingSortCollection = null,
        bool $shouldTreatAsTextSearch = false
    ) {
        $this->filterQueries = $filterQueries;
        $this->fields = $fields;
        $this->pageNumber = $pageNumber;
        $this->maxPerPage = $maxPerPage;
        $this->facetsToExclude = $facetsToExclude;
        $this->sortCollection = $sortCollection ?: new EmptySortCollection();
        $this->groupingField = $groupingField;
        $this->groupingSortCollection = $groupingSortCollection;
        $this->shouldTreatAsTextSearch = $shouldTreatAsTextSearch;
        $this->searchTerm = $searchTerm;
    }

    /**
     * @inheritDoc
     */
    public function getFilterQueries(): array
    {
        return $this->filterQueries;
    }

    /**
     * @inheritDoc
     */
    public function hasFilterQueries(): bool
    {
        return count($this->filterQueries) > 0;
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
    public function getSortCollection(): SortCollectionInterface
    {
        if (!$this->sortCollection instanceof SortCollectionInterface) {
            return new EmptySortCollection();
        }

        return $this->sortCollection;
    }

    public function hasSortCollection(): bool
    {
        return !$this->sortCollection instanceof EmptySortCollection;
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
        foreach ($this->filterQueries as $filterQuery) {
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
    public function doesValueExistInFilterQueries(string $key, $value)
    {
        $fq = $this->getFilterQueryWithKey($key);

        if (!$fq) {
            return false;
        }

        $filterValue = $fq->getFilterValue();

        if ($filterValue instanceof CombinedFilterValueInterface) {
            foreach ($filterValue->getValues() as $v) {
                if ($v->getSearchValue() === strval($value)) {
                    return true;
                }
            }
        }

        if ($filterValue instanceof FilterValueInterface) {
            //cast each value to string for comparison
            return strval($filterValue->getSearchValue()) === strval($value);
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function shouldTreatAsTextSearch()
    {
        return $this->shouldTreatAsTextSearch;
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
    public function hasSearchTerm()
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
}
