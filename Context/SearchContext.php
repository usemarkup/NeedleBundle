<?php

namespace Markup\NeedleBundle\Context;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Collator\NullCollatorProvider;
use Markup\NeedleBundle\Facet\NullSortOrderProvider;
use Markup\NeedleBundle\Intercept\NullInterceptor;
use Markup\NeedleBundle\Query\SelectQueryInterface;
use Markup\NeedleBundle\Sort\EmptySortCollection;

/**
 * A context for searches, providing information that can determine how searches display, which is agnostic of search engine implementations.
 */
class SearchContext implements SearchContextInterface
{
    /**
     * @var int
     **/
    private $itemsPerPage;

    /**
     * @param int $itemsPerPage
     **/
    public function __construct($itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
    }

    /**
     * Gets the number of items to be used on a page.
     *
     * @return int
     **/
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * Gets the set of facets that should be requested with this context.
     *
     * @return \Markup\NeedleBundle\Attribute\AttributeInterface[]
     **/
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
    public function getDefaultSortCollectionForQuery(SelectQueryInterface $query)
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
    public function getSetDecoratorForFacet(AttributeInterface $facet)
    {
        return false;
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

    public function shouldUseFuzzyMatching()
    {
        return false;
    }
}
