<?php

namespace Markup\NeedleBundle\Context;

use Markup\NeedleBundle\Facet\FacetProviderInterface;

/**
 * A search context decorator that makes the context use the available filter names for facets.
 */
class UseAvailableFiltersAsFacetsContextDecorator implements SearchContextInterface
{
    use DecorateContextTrait;

    /**
     * @var FacetProviderInterface
     **/
    private $facetProvider;

    public function __construct(
        SearchContextInterface $searchContext,
        FacetProviderInterface $facetProvider
    ) {
        $this->context = $searchContext;
        $this->facetProvider = $facetProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getFacets()
    {
        $facets = [];
        foreach ($this->getAvailableFilterNames() as $filterName) {
            $facet = $this->facetProvider->getFacetByName($filterName);
            if (!$facet) {
                continue;
            }
            $facets[] = $facet;
        }

        return $facets;
    }
}
