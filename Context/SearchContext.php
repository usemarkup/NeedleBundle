<?php

namespace Markup\NeedleBundle\Context;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Collator\NullCollatorProvider;
use Markup\NeedleBundle\Facet\FacetSetDecoratorInterface;
use Markup\NeedleBundle\Facet\NullSortOrderProvider;
use Markup\NeedleBundle\Intercept\NullInterceptor;
use Markup\NeedleBundle\Sort\SortCollectionInterface;

/**
 * A context for searches, providing information that can determine how searches display, which is agnostic of search engine implementations.
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
     * @var SortCollectionInterface
     */
    private $defaultSortCollection;

    /**
     * @var DefaultContextOptionsInterface|null
     */
    private $defaultContextOptions;

    public function __construct(
        ?int $itemsPerPage,
        array $facets,
        array $defaultFilterQueries,
        SortCollectionInterface $defaultSortCollection,
        ?DefaultContextOptionsInterface $defaultContextOptions = null
    ) {
        $this->itemsPerPage = $itemsPerPage;
        $this->facets = $facets;
        $this->defaultFilterQueries = $defaultFilterQueries;
        $this->defaultSortCollection = $defaultSortCollection;
        $this->defaultContextOptions = $defaultContextOptions;
    }

    /**
     * @inheritDoc
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * @inheritDoc
     */
    public function getFacets()
    {
        return $this->facets;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultFilterQueries()
    {
        return $this->defaultFilterQueries;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultSortCollectionForQuery()
    {
        return $this->defaultSortCollection;
    }

    /**
     * @inheritDoc
     */
    public function getSetDecoratorForFacet(AttributeInterface $facet): ?FacetSetDecoratorInterface
    {
        if (!$this->defaultContextOptions) {
            return null;
        }

        return $this->defaultContextOptions->getSetDecoratorForFacet($facet);
    }

    /**
     * @inheritDoc
     */
    public function getWhetherFacetIgnoresCurrentFilters(AttributeInterface $facet)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getAvailableFilterNames()
    {
        return $this->getFacets();
    }

    /**
     * @inheritDoc
     */
    public function getBoostQueryFields()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getFacetCollatorProvider()
    {
        return new NullCollatorProvider();
    }

    /**
     * @inheritDoc
     */
    public function getFacetSortOrderProvider()
    {
        return new NullSortOrderProvider();
    }

    /**
     * @inheritDoc
     */
    public function getInterceptor()
    {
        return new NullInterceptor();
    }

    /**
     * @inheritDoc
     */
    public function shouldRequestFacetValueForMissing()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function shouldUseFuzzyMatching()
    {
        return false;
    }
}
