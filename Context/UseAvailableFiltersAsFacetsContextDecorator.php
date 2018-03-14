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
            $facets[] = $this->facetProvider->getFacetByName($filterName);
        }

        return $facets;
    }
}
