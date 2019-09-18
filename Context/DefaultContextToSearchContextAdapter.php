<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Context;

use Markup\NeedleBundle\Attribute\AttributeProviderInterface;
use Markup\NeedleBundle\Attribute\SpecializationContextHashInterface;
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
     * @var SpecializationContextHashInterface
     */
    private $contextHash;

    /**
     * @var ContextSortAttributeFactory
     */
    private $sortFactory;

    /**
     * @var ContextFilterFactory
     */
    private $filterFactory;

    public function __construct(
        AttributeProviderInterface $attributeProvider,
        SpecializationContextHashInterface $contextHash
    ) {
        $this->attributeProvider = $attributeProvider;
        $this->contextHash = $contextHash;
        $this->sortFactory = new ContextSortAttributeFactory($this->attributeProvider);
        $this->filterFactory = new ContextFilterFactory($this->attributeProvider, $contextHash);
    }

    public function adapt(
        DefaultContextInterface $defaultContext,
        bool $isSearchTermQuery
    ): SearchContextInterface {
        $sortCollection = new SortCollection();

        foreach ($defaultContext->getDefaultSorts($isSearchTermQuery) as $attrName => $direction) {
            //allow legacy format (using hash as if it is ordered - which it is in PHP but not in other languages like JavaScript)
            if (is_array($direction)) {
                foreach ($direction as $attrKey => $dir) {
                    $sortCollection->add($this->sortFactory->create($this->contextHash, $attrKey, $dir));
                }

                continue;
            }

            $sort = $this->sortFactory->create(
                $this->contextHash,
                $attrName,
                $direction
            );

            $sortCollection->add($sort);
        }

        $filterQueries = [];

        foreach ($defaultContext->getDefaultFilterQueries() as $filter => $filterValue) {
            $filterQuery = $this->filterFactory->create($filter, $filterValue);

            if (!$filterQuery) {
                continue;
            }

            $filterQueries[] = $filterQuery;
        }

        $defaultOptions = null;

        if ($defaultContext instanceof DefaultContextOptionsInterface) {
            $defaultOptions = $defaultContext;
        }

        return new SearchContext(
            $defaultContext->getItemsPerPage(),
            $this->getFacets($defaultContext),
            $filterQueries,
            $sortCollection,
            $defaultOptions
        );
    }

    private function getFacets(DefaultContextInterface $configuredContext): array
    {
        $facets = [];

        foreach ($configuredContext->getFacets() as $facet) {
            $attribute = $this->attributeProvider->getAttributeByName($facet, $this->contextHash);

            if (!$attribute) {
                continue;
            }

            $facets[$attribute->getSearchKey()] = $attribute;
        }

        return $facets;
    }
}
