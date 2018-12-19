<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Result;

use Markup\NeedleBundle\Context\SearchContextInterface;
use Markup\NeedleBundle\Query\SelectQueryInterface;

class ElasticsearchFacetSetsStrategy implements FacetSetStrategyInterface
{
    /**
     * @var array
     */
    private $aggregationsData;

    /**
     * @var SearchContextInterface
     */
    private $searchContext;

    /**
     * @var SelectQueryInterface|null
     */
    private $originalQuery;

    public function __construct(
        array $aggregationsData,
        SearchContextInterface $searchContext,
        ?SelectQueryInterface $originalQuery = null
    ) {
        $this->aggregationsData = $aggregationsData;
        $this->searchContext = $searchContext;
        $this->originalQuery = $originalQuery;
    }

    /**
     * @return \Markup\NeedleBundle\Facet\FacetSetInterface[]|ElasticsearchFacetSetsIterator
     **/
    public function getFacetSets()
    {
        return new ElasticsearchFacetSetsIterator(
            $this->flattenAggregationsData($this->aggregationsData),
            $this->searchContext,
            $this->originalQuery
        );
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
