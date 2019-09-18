<?php

namespace Markup\NeedleBundle\Query;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Collator\CollatorProviderInterface;
use Markup\NeedleBundle\Collator\NullCollatorProvider;
use Markup\NeedleBundle\Context\NoopSearchContext;
use Markup\NeedleBundle\Context\SearchContext;
use Markup\NeedleBundle\Context\SearchContextInterface;
use Markup\NeedleBundle\Filter\FilterQueryInterface;
use Markup\NeedleBundle\Sort\EmptySortCollection;
use Markup\NeedleBundle\Sort\SortCollectionInterface;

/**
 * {@inheritdoc}
 */
class ResolvedSelectQuery implements ResolvedSelectQueryInterface
{
    /**
     * @var SelectQueryInterface
     */
    private $selectQuery;

    /**
     * @var SearchContextInterface
     */
    private $searchContext;

    public function __construct(
        SelectQueryInterface $selectQuery,
        ?SearchContextInterface $searchContext = null
    ) {
        $this->selectQuery = $selectQuery;
        $this->searchContext = $searchContext ?: new NoopSearchContext();
    }

    protected function getSelectQuery(): SelectQueryInterface
    {
        return $this->selectQuery;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchContext(): SearchContextInterface
    {
        return $this->searchContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterQueries(): array
    {
        $fq = $this->getSelectQuery()->getFilterQueries();

        return array_merge($fq, $this->getSearchContext()->getDefaultFilterQueries());
    }

    /**
     * {@inheritdoc}
     */
    public function hasFilterQueries(): bool
    {
        return count($this->getFilterQueries()) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields(): array
    {
        return $this->getSelectQuery()->getFields();
    }

    /**
     * {@inheritdoc}
     */
    public function getPageNumber(): int
    {
        return $this->getSelectQuery()->getPageNumber() ?: 1;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxPerPage(): int
    {
        $maxPerPage = $this->getSelectQuery()->getMaxPerPage();

        if (is_int($maxPerPage)) {
            return $maxPerPage;
        }

        return $this->getSearchContext()->getItemsPerPage() ?: 9999;
    }

    /**
     * {@inheritdoc}
     */
    public function getSortCollection(): SortCollectionInterface
    {
        if ($this->getSelectQuery()->hasSortCollection()) {
            $sort = $this->getSelectQuery()->getSortCollection();

            if ($sort !== null) {
                return $sort;
            }
        }

        return $this->getSearchContext()->getDefaultSortCollectionForQuery();
    }

    /**
     * {@inheritdoc}
     */
    public function hasSortCollection(): bool
    {
        return $this->getSelectQuery()->hasSortCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getFacetsToExclude(): array
    {
        return $this->getSelectQuery()->getFacetsToExclude();
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterQueryWithKey(string $key): ?FilterQueryInterface
    {
        return $this->getSelectQuery()->getFilterQueryWithKey($key);
    }

    /**
     * {@inheritdoc}
     */
    public function doesValueExistInFilterQueries($key, $value)
    {
        return $this->getSelectQuery()->doesValueExistInFilterQueries($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function shouldTreatAsTextSearch(): bool
    {
        return $this->getSelectQuery()->shouldTreatAsTextSearch();
    }

    /**
     * {@inheritdoc}
     */
    public function getSpellcheck()
    {
        return $this->getSelectQuery()->getSpellcheck();
    }

    /**
     * {@inheritdoc}
     */
    public function hasSearchTerm(): bool
    {
        return $this->getSelectQuery()->hasSearchTerm();
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchTerm(): string
    {
        $searchTerm = $this->getSelectQuery()->getSearchTerm();

        if (!$this->hasSearchTerm() || !is_string($searchTerm)) {
            throw new \RuntimeException('No search term provided');
        }

        return $searchTerm;
    }

    /**
     * {@inheritdoc}
     */
    public function getFacets()
    {
        return $this->getSearchContext()->getFacets();
    }

    /**
     * {@inheritdoc}
     */
    public function shouldRequestFacetValueForMissing()
    {
        return $this->getSearchContext()->shouldRequestFacetValueForMissing();
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrderForFacet(AttributeInterface $facet)
    {
        return $this->getSearchContext()->getFacetSortOrderProvider()->getSortOrderForFacet($facet);
    }

    /**
     * {@inheritdoc}
     */
    public function getWhetherFacetIgnoresCurrentFilters(AttributeInterface $facet)
    {
        return $this->getSearchContext()->getWhetherFacetIgnoresCurrentFilters($facet);
    }

    /**
     * {@inheritdoc}
     */
    public function getBoostQueryFields()
    {
        return $this->getSearchContext()->getBoostQueryFields();
    }

    /**
     * {@inheritdoc}
     */
    public function shouldUseFacetValuesForRecordedQuery()
    {
        if (!$this->getSelectQuery() instanceof RecordableSelectQueryInterface) {
            return false;
        }
        if (!$this->getSelectQuery()->hasRecord()) {
            return false;
        }
        /** @var SelectQueryInterface $record */
        $record = $this->getSelectQuery()->getRecord();

        if (count($this->getSelectQuery()->getFilterQueries()) > count($record->getFilterQueries())) {
            return true;
        }

        return false;
    }

    /**
     * Gets the original select query prior to resolution, for read-only access.
     *
     * @return SelectQueryInterface
     */
    public function getOriginalSelectQuery()
    {
        return $this->getSelectQuery();
    }

    /**
     * {@inheritdoc}
     */
    public function getRecord()
    {
        if (!$this->getSelectQuery() instanceof RecordableSelectQueryInterface) {
            return null;
        }
        if (!$this->getSelectQuery()->hasRecord()) {
            return null;
        }

        return $this->getSelectQuery()->getRecord();
    }

    /**
    * {@inheritdoc}
    */
    public function getGroupingField(): ?AttributeInterface
    {
        return $this->getSelectQuery()->getGroupingField();
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupingSortCollection()
    {
        return $this->getSelectQuery()->getGroupingSortCollection();
    }

    public function shouldUseFuzzyMatching()
    {
        $context = $this->getSearchContext();

        return $context->shouldUseFuzzyMatching();
    }

    public function getFacetCollatorProvider(): CollatorProviderInterface
    {
        $context = $this->getSearchContext();

        return $context->getFacetCollatorProvider();
    }
}
