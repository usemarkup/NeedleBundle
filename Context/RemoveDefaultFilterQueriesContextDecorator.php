<?php

namespace Markup\NeedleBundle\Context;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Query\SelectQueryInterface;

/**
 * A search context decorator that has no default filter queries.
 */
class RemoveDefaultFilterQueriesContextDecorator implements SearchContextInterface
{
    const LARGE_NUMBER = 10000;

    /**
     * @var SearchContextInterface
     **/
    private $searchContext;

    /**
     * @param SearchContextInterface $searchContext The search context being decorated.
     **/
    public function __construct(SearchContextInterface $searchContext)
    {
        $this->searchContext = $searchContext;
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
        return $this->searchContext->getDefaultSortCollectionForQuery($query);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemsPerPage()
    {
        return self::LARGE_NUMBER;
    }

    /**
     * {@inheritdoc}
     */
    public function getFacets()
    {
        return $this->searchContext->getFacets();
    }

    /**
     * {@inheritdoc}
     */
    public function getSetDecoratorForFacet(AttributeInterface $facet)
    {
        return $this->searchContext->getSetDecoratorForFacet($facet);
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
    public function getAvailableFilterNames()
    {
        return $this->searchContext->getAvailableFilterNames();
    }

    /**
     * {@inheritdoc}
     */
    public function getBoostQueryFields()
    {
        return $this->searchContext->getBoostQueryFields();
    }

    /**
     * {@inheritdoc}
     */
    public function getFacetCollatorProvider()
    {
        return $this->searchContext->getFacetCollatorProvider();
    }

    /**
     * {@inheritdoc}
     */
    public function getFacetSortOrderProvider()
    {
        return $this->searchContext->getFacetSortOrderProvider();
    }

    /**
     * {@inheritdoc}
     */
    public function getInterceptor()
    {
        return $this->searchContext->getInterceptor();
    }

    /**
     * {@inheritdoc}
     */
    public function shouldRequestFacetValueForMissing()
    {
        return $this->searchContext->shouldRequestFacetValueForMissing();
    }
}
