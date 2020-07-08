<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Result;

use Markup\NeedleBundle\Collator\CollatorProviderInterface;
use Markup\NeedleBundle\Facet\FacetSetDecoratorProviderInterface;
use Markup\NeedleBundle\Facet\FacetSetInterface;
use Markup\NeedleBundle\Query\SelectQueryInterface;

class ElasticsearchFacetSetsStrategy implements FacetSetStrategyInterface
{
    /**
     * @var array
     */
    private $aggregationsData;

    /**
     * @var SelectQueryInterface|null
     */
    private $originalQuery;

    /**
     * @var array
     */
    private $facets;

    /**
     * @var CollatorProviderInterface
     */
    private $collatorProvider;

    /**
     * @var FacetSetDecoratorProviderInterface
     */
    private $facetSetDecoratorProvider;

    public function __construct(
        array $aggregationsData,
        array $facets,
        CollatorProviderInterface $collatorProvider,
        FacetSetDecoratorProviderInterface $facetSetDecoratorProvider,
        ?SelectQueryInterface $originalQuery = null
    ) {
        $this->aggregationsData = $aggregationsData;
        $this->originalQuery = $originalQuery;
        $this->facets = $facets;
        $this->collatorProvider = $collatorProvider;
        $this->facetSetDecoratorProvider = $facetSetDecoratorProvider;
    }

    public function getFacetSets()
    {
        /** @var FacetSetInterface[] $facetSets */
        $facetSets = new ElasticsearchFacetSetsIterator(
            $this->flattenAggregationsData($this->aggregationsData),
            $this->facets,
            $this->collatorProvider,
            $this->facetSetDecoratorProvider,
            $this->originalQuery
        );

        return $facetSets;
    }

    private function flattenAggregationsData(array $data): array
    {
        $flattened = [];
        foreach ($data as $key => $item) {
            if (array_key_exists('buckets', $item)) {
                $flattened[$key] = $item;
                continue;
            }
            $flattened[$key] = $item[$key];
        }

        return $flattened;
    }
}
