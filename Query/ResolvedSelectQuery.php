<?php

namespace Markup\NeedleBundle\Query;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Context\SearchContextInterface;
use Markup\NeedleBundle\Service\SearchServiceInterface;
use Markup\NeedleBundle\Sort\EmptySortCollection;

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

    /**
     * @param SelectQueryInterface        $selectQuery
     * @param SearchContextInterface|null $searchContext
     */
    public function __construct(
        SelectQueryInterface $selectQuery,
        SearchContextInterface $searchContext = null
    ) {
        $this->selectQuery = $selectQuery;
        $this->searchContext = $searchContext;
    }

    /**
     * @return SelectQueryInterface
     */
    protected function getSelectQuery()
    {
        return $this->selectQuery;
    }

    /**
     * @return SearchContextInterface|null
     */
    protected function getSearchContext()
    {
        return $this->searchContext;
    }

    /**
     * {@inheritdoc}
     * Merges select filterQueries with defaults
     */
    public function getFilterQueries()
    {
        $fq = $this->getSelectQuery()->getFilterQueries();
        if ($this->getSearchContext() === null) {
            return $fq;
        }

        return array_merge($fq, $this->getSearchContext()->getDefaultFilterQueries());
    }

    /**
     * {@inheritdoc}
     */
    public function hasFilterQueries()
    {
        return count($this->getFilterQueries()) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        return $this->getSelectQuery()->getFields();
    }

    /**
     * {@inheritdoc}
     */
    public function getPageNumber()
    {
        return $this->getSelectQuery()->getPageNumber();
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxPerPage()
    {
        return $this->getSelectQuery()->getMaxPerPage();
    }

    /**
     * {@inheritdoc}
     */
    public function getSortCollection()
    {
        if ($this->getSelectQuery()->hasSortCollection()) {
            return $this->getSelectQuery()->getSortCollection();
        }
        if ($this->getSearchContext() === null) {
            return new EmptySortCollection();
        }

        return $this->getSearchContext()->getDefaultSortCollectionForQuery($this->getSelectQuery());
    }

    /**
     * {@inheritdoc}
     */
    public function hasSortCollection()
    {
        return $this->getSelectQuery()->hasSortCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getFacetNamesToExclude()
    {
        return $this->getSelectQuery()->getFacetNamesToExclude();
    }

    /**
     * {@inheritdoc}
     */
    public function getResult()
    {
        return $this->getSelectQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function setSearchService(SearchServiceInterface $service)
    {
        return $this->getSelectQuery()->setSearchService($service);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterQueryWithKey($key)
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
    public function shouldTreatAsTextSearch()
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
    public function hasSearchTerm()
    {
        return $this->getSelectQuery()->hasSearchTerm();
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchTerm()
    {
        return $this->getSelectQuery()->getSearchTerm();
    }

    /**
     * {@inheritdoc}
     */
    public function getFacets()
    {
        if ($this->getSearchContext() === null) {
            return [];
        }

        return $this->getSearchContext()->getFacets();
    }

    /**
     * {@inheritdoc}
     */
    public function shouldRequestFacetValueForMissing()
    {
        if ($this->getSearchContext() === null) {
            return false;
        }

        return $this->getSearchContext()->shouldRequestFacetValueForMissing();
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrderForFacet(AttributeInterface $facet)
    {
        if ($this->getSearchContext() === null) {
            return;
        }

        return $this->getSearchContext()->getFacetSortOrderProvider()->getSortOrderForFacet($facet);
    }

    /**
     * {@inheritdoc}
     */
    public function getWhetherFacetIgnoresCurrentFilters(AttributeInterface $facet)
    {
        if ($this->getSearchContext() === null) {
            return false;
        }

        return $this->getSearchContext()->getWhetherFacetIgnoresCurrentFilters($facet);
    }

    /**
     * {@inheritdoc}
     */
    public function getBoostQueryFields()
    {
        if ($this->getSearchContext() === null) {
            return [];
        }

        return $this->getSearchContext()->getBoostQueryFields();
    }

    /**
     * {@inheritdoc}
     */
    public function shouldUseFacetValuesForRecordedQuery()
    {
        if ($this->getSearchContext() === null) {
            return false;
        }
        if (!$this->getSelectQuery() instanceof RecordableSelectQueryInterface) {
            return false;
        }
        if (!$this->getSelectQuery()->hasRecord()) {
            return false;
        }

        if (count($this->getSelectQuery()->getFilterQueries()) > count($this->getSelectQuery()->getRecord()->getFilterQueries())) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecord()
    {
        if (!$this->getSelectQuery() instanceof RecordableSelectQueryInterface) {
            return;
        }
        if (!$this->getSelectQuery()->hasRecord()) {
            return;
        }

        return $this->getSelectQuery()->getRecord();
    }

    /**
 * {@inheritdoc}
 */
    public function getGroupingField()
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
}
