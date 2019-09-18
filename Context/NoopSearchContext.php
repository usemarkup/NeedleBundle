<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Context;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Collator\NullCollatorProvider;
use Markup\NeedleBundle\Facet\FacetSetDecoratorInterface;
use Markup\NeedleBundle\Facet\NullSortOrderProvider;
use Markup\NeedleBundle\Intercept\NullInterceptor;
use Markup\NeedleBundle\Sort\EmptySortCollection;

class NoopSearchContext implements SearchContextInterface
{
    /**
     * {@inheritdoc}
     */
    public function getItemsPerPage()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getFacets()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultFilterQueries()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultSortCollectionForQuery()
    {
        return new EmptySortCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getWhetherFacetIgnoresCurrentFilters(AttributeInterface $facet)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getSetDecoratorForFacet(AttributeInterface $facet): ?FacetSetDecoratorInterface
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableFilterNames()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getBoostQueryFields()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getFacetCollatorProvider()
    {
        return new NullCollatorProvider();
    }

    /**
     * {@inheritdoc}
     */
    public function getFacetSortOrderProvider()
    {
        return new NullSortOrderProvider();
    }

    /**
     * {@inheritdoc}
     */
    public function getInterceptor()
    {
        return new NullInterceptor();
    }

    /**
     * {@inheritdoc}
     */
    public function shouldRequestFacetValueForMissing()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function shouldUseFuzzyMatching()
    {
        return false;
    }
}
