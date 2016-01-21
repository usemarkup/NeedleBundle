<?php

namespace Markup\NeedleBundle\Context;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Facet\FacetProviderInterface;
use Markup\NeedleBundle\Query\SelectQueryInterface;

/**
 * A search context decorator that makes the context use the available filter names for facets.
 */
class UseAvailableFiltersAsFacetsContextDecorator implements SearchContextInterface
{
    /**
     * The context being decorated.
     *
     * @var SearchContextInterface
     **/
    private $searchContext;

    /**
     * @var FacetProviderInterface
     **/
    private $facetProvider;

    /**
     * @param SearchContextInterface $searchContext
     * @param FacetProviderInterface $facetProvider
     **/
    public function __construct(
        SearchContextInterface $searchContext,
        FacetProviderInterface $facetProvider
    ) {
        $this->searchContext = $searchContext;
        $this->facetProvider = $facetProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getFacets()
    {
        $facets = [];
        foreach ($this->getAvailableFilterNames() as $filterName) {
            $facets[] = $this->facetProvider->getFacetByName($filterName);
        }

        return $facets;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemsPerPage()
    {
        return $this->searchContext->getItemsPerPage();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultFilterQueries()
    {
        return $this->searchContext->getDefaultFilterQueries();
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
