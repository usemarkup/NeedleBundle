<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Context;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Collator\CollatorProviderInterface;
use Markup\NeedleBundle\Collator\NullCollatorProvider;
use Markup\NeedleBundle\Facet\FacetSetDecoratorProviderInterface;
use Markup\NeedleBundle\Facet\NullFacetSetDecoratorProvider;
use Markup\NeedleBundle\Facet\NullSortOrderProvider;
use Markup\NeedleBundle\Facet\SortOrderProviderInterface;
use Markup\NeedleBundle\Intercept\InterceptorInterface;
use Markup\NeedleBundle\Intercept\NullInterceptor;
use Markup\NeedleBundle\Sort\EmptySortCollection;
use Markup\NeedleBundle\Sort\SortCollectionInterface;

class NoopSearchContext implements SearchContextInterface
{
    /**
     * {@inheritdoc}
     */
    public function getItemsPerPage(): ?int
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultFacets(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultFilterQueries(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultSortCollectionForQuery(): SortCollectionInterface
    {
        return new EmptySortCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getWhetherFacetIgnoresCurrentFilters(AttributeInterface $facet): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getFacetSetDecoratorProvider(): FacetSetDecoratorProviderInterface
    {
        return new NullFacetSetDecoratorProvider();
    }

    /**
     * {@inheritdoc}
     */
    public function getBoostQueryFields(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getFacetCollatorProvider(): CollatorProviderInterface
    {
        return new NullCollatorProvider();
    }

    /**
     * {@inheritdoc}
     */
    public function getFacetSortOrderProvider(): SortOrderProviderInterface
    {
        return new NullSortOrderProvider();
    }

    /**
     * {@inheritdoc}
     */
    public function getInterceptor(): InterceptorInterface
    {
        return new NullInterceptor();
    }

    /**
     * {@inheritdoc}
     */
    public function shouldRequestFacetValueForMissing(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function shouldUseFuzzyMatching(): bool
    {
        return false;
    }
}
