<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Builder;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Facet\SortOrderProviderInterface;
use Markup\NeedleBundle\Filter\FilterQueryInterface;
use Markup\NeedleBundle\Filter\FilterValueInterface;
use Markup\NeedleBundle\Filter\IntersectionFilterValueInterface;
use Markup\NeedleBundle\Filter\RangeFilterValueInterface;
use Markup\NeedleBundle\Filter\UnionFilterValueInterface;
use Markup\NeedleBundle\Query\ResolvedSelectQueryInterface;

/**
 * A query builder for Elasticsearch, to adapt the abstract Needle query format.
 */
class ElasticSelectQueryBuilder
{
    use DedupeFilterQueryTrait;

    public function buildElasticQueryFromGeneric(ResolvedSelectQueryInterface $genericQuery): array
    {
        $query = [];

        if ($genericQuery->hasSearchTerm()) {
            $matchClause = [
                'multi_match' => [
                    'query' => $genericQuery->getSearchTerm(),
                ],
            ];
        } else {
            $matchClause = [
                'match_all' => new \stdClass(),
            ];
        }

        //determine whether we are using the facet values for an underlying "base" (recorded) query
        $shouldUseFacetValuesForRecordedQuery = $genericQuery->shouldUseFacetValuesForRecordedQuery();

        //if there are filter queries, add them
        $filterQueries = $genericQuery->getFilterQueries();
        if (count($filterQueries) > 0) {
            $filterQueries = $this->dedupeFilterQueries($filterQueries);

            $mustClause = [$matchClause];
            foreach ($filterQueries as $filterQuery) {
                /** FilterQueryInterface $filterQuery */
                $clause = $this->getQueryShapeForFilterQuery(
                    $filterQuery->getSearchKey(),
                    $filterQuery->getFilterValue()
                );
                if ($clause !== null) {
                    $mustClause[] = $clause;
                }
            }
            $query['query']['constant_score']['filter']['bool']['must'] = $mustClause;
        } else {
            $query['query'] = $matchClause;
        }

        //if there are fields specified, set them
        $fields = $genericQuery->getFields();
        if (!empty($fields)) {
            $query['_source'] = array_map(
                function ($field) {
                    if ($field instanceof AttributeInterface) {
                        return $field->getSearchKey(['prefer_parsed' => false]);
                    }

                    return strval($field);
                },
                $fields
            );
        }

        //if there are facets to request, request them
        $facets = $genericQuery->getFacets();
        if (!empty($facets)) {
            $facetNamesToExclude = $genericQuery->getFacetNamesToExclude();
            $shouldIncludeFacetValuesForMissing = $genericQuery->shouldRequestFacetValueForMissing();
            foreach ($facets as $facet) {
                //if query indicates we should skip this facet, then skip it
                if (false !== array_search($facet->getSearchKey(), $facetNamesToExclude)) {
                    continue;
                }
                $query['aggs'][$facet->getName()] = [
                    'terms' => [
                        'field' => $facet->getSearchKey(['prefer_parsed' => false]),
                        'min_doc_count' => ($shouldIncludeFacetValuesForMissing) ? 0 : 1,
                        'order' => ($genericQuery->getSortOrderForFacet($facet) === SortOrderProviderInterface::SORT_BY_COUNT)
                            ? ['_count' => 'asc']
                            : ['_key' => 'asc']
                    ],
                ];
            }
        }

        return $query;
    }

    private function getQueryShapeForFilterQuery(string $searchKey, FilterValueInterface $filterValue): ?array
    {
        switch ($filterValue->getValueType()) {
            case FilterValueInterface::TYPE_SIMPLE:
                return [
                    'term' => [
                        $searchKey => $filterValue->getSearchValue(),
                    ],
                ];
            case FilterValueInterface::TYPE_UNION:
                /** @var UnionFilterValueInterface $unionValue */
                $unionValue = $filterValue;

                return [
                    'bool' => [
                        'should' => array_map(
                            function (FilterValueInterface $filterValue) use ($searchKey) {
                                return $this->getQueryShapeForFilterQuery($searchKey, $filterValue);
                            },
                            $unionValue->getValues()
                        ),
                    ],
                ];
            case FilterValueInterface::TYPE_INTERSECTION:
                /** @var IntersectionFilterValueInterface $intersectionValue */
                $intersectionValue = $filterValue;

                return [
                    'bool' => [
                        'must' => array_map(
                            function (FilterValueInterface $filterValue) use ($searchKey) {
                                return $this->getQueryShapeForFilterQuery($searchKey, $filterValue);
                            },
                            $intersectionValue->getValues()
                        )
                    ],
                ];
            case FilterValueInterface::TYPE_RANGE:
                /** @var RangeFilterValueInterface $rangeValue */
                $rangeValue = $filterValue;

                return [
                    'range' => [
                        $searchKey => [
                            'gte' => $rangeValue->getMin(),
                            'lte' => $rangeValue->getMax(),
                        ],
                    ],
                ];
            default:
                return null;
                break;
        }
    }
}
