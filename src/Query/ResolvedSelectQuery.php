<?php

namespace Markup\NeedleBundle\Query;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Builder\DedupeFilterQueryTrait;
use Markup\NeedleBundle\Collator\CollatorProviderInterface;
use Markup\NeedleBundle\Context\NoopSearchContext;
use Markup\NeedleBundle\Context\SearchContextInterface;
use Markup\NeedleBundle\Facet\FacetSetDecoratorProviderInterface;
use Markup\NeedleBundle\Filter\FilterQueryInterface;
use Markup\NeedleBundle\Sort\DefinedSortOrder;
use Markup\NeedleBundle\Sort\SortCollectionInterface;

/**
 *
 * {@inheritdoc}
 */
class ResolvedSelectQuery implements ResolvedSelectQueryInterface
{
    use DedupeFilterQueryTrait;

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
    public function getFilterQueries(): array
    {
        return $this->dedupeFilterQueries(array_merge(
            $this->getSelectQuery()->getFilterQueries(),
            $this->searchContext->getDefaultFilterQueries()
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getContextFilterQueries(): array
    {
        return $this->searchContext->getDefaultFilterQueries();
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseFilterQueries(): array
    {
        return $this->getSelectQuery()->getBaseFilterQueries();
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseAndContextFilterQueries(): array
    {
        return $this->dedupeFilterQueries(array_merge(
            $this->searchContext->getDefaultFilterQueries(),
            $this->getSelectQuery()->getBaseFilterQueries()
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getAppliedFilterQueries(): array
    {
        return $this->getSelectQuery()->getAppliedFilterQueries();
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
    public function getMappingHashForFields(): array
    {
        $hash = [];
        foreach ($this->getFields() as $name => $attribute) {
            if (!is_string($name) || !$attribute instanceof AttributeInterface) {
                throw new \InvalidArgumentException('Bad fields defined for query');
            }

            $hash[$name] = $attribute->getSearchKey();
        }

        return $hash;
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

        return $this->searchContext->getItemsPerPage() ?: 9999;
    }

    /**
     * {@inheritdoc}
     */
    public function getSortCollection(): ?SortCollectionInterface
    {
        if ($this->getSelectQuery()->hasSortCollection()) {
            $sort = $this->getSelectQuery()->getSortCollection();

            if ($sort !== null) {
                return $sort;
            }
        }
        
        // only use the fallback from the search context in very specific cases...
        // most of the time you would not want to use this
        
        if ($this->hasSearchTerm()) {
            // case where "text searching" but user has not picked a sort order
            return null;
        }

        if ($this->getDefinedSortOrder()) {
            // if 'definedSortOrder' has been defined then we don't want a fallback sort order
            return null;
        }
        
        return $this->searchContext->getDefaultSortCollectionForQuery();
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
    public function getFacets(): array
    {
        $facets = $this->selectQuery->getFacets();
        
        return $facets ?? $this->searchContext->getDefaultFacets();
    }

    /**
     * {@inheritdoc}
     */
    public function shouldRequestFacetValueForMissing()
    {
        return $this->searchContext->shouldRequestFacetValueForMissing();
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrderForFacet(AttributeInterface $facet)
    {
        return $this->searchContext->getFacetSortOrderProvider()->getSortOrderForFacet($facet);
    }

    /**
     * {@inheritdoc}
     */
    public function getWhetherFacetIgnoresCurrentFilters(AttributeInterface $facet)
    {
        return $this->searchContext->getWhetherFacetIgnoresCurrentFilters($facet);
    }

    /**
     * {@inheritdoc}
     */
    public function getBoostQueryFields(): array
    {
        $boosts = $this->searchContext->getBoostQueryFields();
        if (!empty($boosts)) {
            return $boosts;
        }

        return [];
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

    /**
     * {@inheritdoc}
     */
    public function shouldUseFuzzyMatching(): bool
    {
        return $this->searchContext->shouldUseFuzzyMatching();
    }

    /**
     * {@inheritdoc}
     */
    public function getFacetCollatorProvider(): CollatorProviderInterface
    {
        return $this->searchContext->getFacetCollatorProvider();
    }

    /**
     * {@inheritdoc}
     */
    public function getFacetSetDecoratorProvider(): FacetSetDecoratorProviderInterface
    {
        return $this->searchContext->getFacetSetDecoratorProvider();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinedSortOrder(): ?DefinedSortOrder
    {
        return $this->getSelectQuery()->getDefinedSortOrder();
    }
}
