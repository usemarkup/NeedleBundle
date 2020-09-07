<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Context;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Boost\BoostQueryFieldInterface;
use Markup\NeedleBundle\Collator\CollatorProviderInterface;
use Markup\NeedleBundle\Collator\NullCollatorProvider;
use Markup\NeedleBundle\Facet\FacetSetDecoratorProviderInterface;
use Markup\NeedleBundle\Facet\NullFacetSetDecoratorProvider;
use Markup\NeedleBundle\Facet\NullSortOrderProvider;
use Markup\NeedleBundle\Facet\SortOrderProviderInterface;
use Markup\NeedleBundle\Filter\FilterQueryInterface;
use Markup\NeedleBundle\Intercept\InterceptorInterface;
use Markup\NeedleBundle\Intercept\NullInterceptor;
use Markup\NeedleBundle\Sort\SortCollectionInterface;

/**
 * A context for searches
 *
 * This provides the same information as DefaultContextInterface but with scalars converted to Attribute objects
 */
class SearchContext implements SearchContextInterface
{
    /**
     * @var int|null
     */
    private $itemsPerPage;

    /**
     * @var array
     */
    private $facets;

    /**
     * @var array
     */
    private $defaultFilterQueries;

    /**
     * @var SortCollectionInterface|null
     */
    private $defaultSortCollection;

    /**
     * @var bool
     */
    private $facetIgnoresFilters;

    /**
     * @var CollatorProviderInterface
     */
    private $facetCollatorProvider;

    /**
     * @var FacetSetDecoratorProviderInterface
     */
    private $facetSetDecoratorProvider;

    /**
     * @var array
     */
    private $boostQueryFields;

    public function __construct(
        ?int $itemsPerPage,
        array $facets,
        array $defaultFilterQueries,
        ?SortCollectionInterface $defaultSortCollection,
        array $boosts,
        bool $facetIgnoresFilters,
        ?CollatorProviderInterface $facetCollatorProvider,
        ?FacetSetDecoratorProviderInterface $facetSetDecoratorProvider
    ) {
        foreach ($facets as $facet) {
            if (!$facet instanceof AttributeInterface) {
                throw new \InvalidArgumentException('$facets is expected to be an array of AttributeInterface');
            }
        }
        foreach ($defaultFilterQueries as $filter) {
            if (!$filter instanceof FilterQueryInterface) {
                throw new \InvalidArgumentException('$filter is expected to be an array of FilterQueryInterface');
            }
        }
        foreach ($boosts as $boost) {
            if (!$boost instanceof BoostQueryFieldInterface) {
                throw new \InvalidArgumentException('$boosts is expected to be an array of BoostQueryFieldInterface');
            }
        }

        $this->itemsPerPage = $itemsPerPage;
        $this->facets = $facets;
        $this->defaultFilterQueries = $defaultFilterQueries;
        $this->defaultSortCollection = $defaultSortCollection;
        $this->facetIgnoresFilters = $facetIgnoresFilters;
        $this->facetCollatorProvider = $facetCollatorProvider ?? new NullCollatorProvider();
        $this->facetSetDecoratorProvider = $facetSetDecoratorProvider ?? new NullFacetSetDecoratorProvider();
        $this->boostQueryFields = $boosts;
    }

    /**
     * @inheritDoc
     */
    public function getItemsPerPage(): ?int
    {
        return $this->itemsPerPage;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultFacets(): array
    {
        return $this->facets;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultFilterQueries(): array
    {
        return $this->defaultFilterQueries;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultSortCollectionForQuery(): ?SortCollectionInterface
    {
        return $this->defaultSortCollection;
    }

    /**
     * @inheritDoc
     */
    public function getFacetSetDecoratorProvider(): FacetSetDecoratorProviderInterface
    {
        return $this->facetSetDecoratorProvider;
    }

    /**
     * @inheritDoc
     */
    public function getWhetherFacetIgnoresCurrentFilters(AttributeInterface $facet): bool
    {
        return $this->facetIgnoresFilters;
    }

    /**
     * @inheritDoc
     */
    public function getBoostQueryFields(): array
    {
        return $this->boostQueryFields;
    }

    /**
     * @inheritDoc
     */
    public function getFacetCollatorProvider(): CollatorProviderInterface
    {
        return $this->facetCollatorProvider;
    }

    /**
     * @inheritDoc
     */
    public function getFacetSortOrderProvider(): SortOrderProviderInterface
    {
        return new NullSortOrderProvider();
    }

    /**
     * @inheritDoc
     */
    public function getInterceptor(): InterceptorInterface
    {
        return new NullInterceptor();
    }

    /**
     * @inheritDoc
     */
    public function shouldRequestFacetValueForMissing(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function shouldUseFuzzyMatching(): bool
    {
        return false;
    }
}
