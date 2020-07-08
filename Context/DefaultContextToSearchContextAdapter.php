<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Context;

use Markup\NeedleBundle\Attribute\AttributeProviderInterface;
use Markup\NeedleBundle\Attribute\SpecializationContextHashInterface;
use Markup\NeedleBundle\Boost\BoostQueryField;
use Markup\NeedleBundle\Sort\SortCollection;

/**
 * Composes a SearchContext from a ConfiguredContext during runtime for a given ContextMap
 */
class DefaultContextToSearchContextAdapter
{
    /**
     * @var AttributeProviderInterface
     */
    private $attributeProvider;

    /**
     * @var ContextSortAttributeFactory
     */
    private $sortFactory;

    /**
     * @var ContextFilterFactory
     */
    private $filterFactory;

    /**
     * @var AttributeProviderInterface
     */
    private $facetProvider;

    public function __construct(
        AttributeProviderInterface $attributeProvider,
        AttributeProviderInterface $facetProvider
    ) {
        $this->attributeProvider = $attributeProvider;
        $this->facetProvider = $facetProvider;
        $this->sortFactory = new ContextSortAttributeFactory($this->attributeProvider);
        $this->filterFactory = new ContextFilterFactory($this->attributeProvider);
    }

    public function adapt(
        DefaultContextInterface $defaultContext,
        SpecializationContextHashInterface $contextHash
    ): SearchContextInterface {
        $sortCollection = new SortCollection();

        foreach ($defaultContext->getDefaultSorts() as $attrName => $direction) {
            //allow legacy format (using hash as if it is ordered)
            if (is_array($direction)) {
                foreach ($direction as $attrKey => $dir) {
                    $sortCollection->add($this->sortFactory->create($contextHash, $attrKey, $dir));
                }

                continue;
            }

            $sort = $this->sortFactory->create(
                $contextHash,
                $attrName,
                $direction
            );

            $sortCollection->add($sort);
        }

        $filterQueries = [];

        // TODO: Filter factory is currently hardcoded to provide filters as facets....
        // maybe it needs to be able to provide as attributes directly OR as facets?
        foreach ($defaultContext->getDefaultFilterQueries() as $filter => $filterValue) {
            $filterQuery = $this->filterFactory->create($filter, $filterValue, $contextHash);

            if (!$filterQuery) {
                continue;
            }

            $filterQueries[] = $filterQuery;
        }

        $defaultOptions = null;


        return new SearchContext(
            $defaultContext->getItemsPerPage(),
            $this->getFacets($defaultContext, $contextHash),
            $filterQueries,
            $sortCollection,
            $this->getBoosts($defaultContext, $contextHash),
            true,
            $defaultContext->getFacetCollatorProvider(),
            $defaultContext->getFacetSetDecoratorProvider()
        );
    }

    private function getBoosts(
        DefaultContextInterface $configuredContext,
        SpecializationContextHashInterface $contextHash
    ): array {
        $boosts = [];
        foreach ($configuredContext->getBoostQueryFields() as $boost => $boostFactor) {
            $attribute = $this->attributeProvider->getAttributeByName($boost, $contextHash);

            $boosts[$attribute->getSearchKey()] = new BoostQueryField($attribute, $boostFactor);
        }

        return $boosts;
    }

    private function getFacets(
        DefaultContextInterface $configuredContext,
        SpecializationContextHashInterface $contextHash
    ): array {
        $facets = [];

        foreach ($configuredContext->getDefaultFacets() as $facet) {
            $attribute = $this->facetProvider->getAttributeByName($facet, $contextHash);

            $facets[$attribute->getSearchKey()] = $attribute;
        }

        return $facets;
    }
}
