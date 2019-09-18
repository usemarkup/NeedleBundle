<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Builder;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Elastic\QueryShapeBuilder;
use Markup\NeedleBundle\Exception\UnformableSearchKeyException;
use Markup\NeedleBundle\Facet\SortOrderProviderInterface;
use Markup\NeedleBundle\Filter\FilterQueryInterface;
use Markup\NeedleBundle\Filter\FilterValueInterface;
use Markup\NeedleBundle\Filter\IntersectionFilterValueInterface;
use Markup\NeedleBundle\Filter\RangeFilterValueInterface;
use Markup\NeedleBundle\Filter\UnionFilterValueInterface;
use Markup\NeedleBundle\Lucene\BoostLucenifier;
use Markup\NeedleBundle\Query\ResolvedSelectQueryInterface;
use Markup\NeedleBundle\Sort\SortCollectionInterface;
use Markup\NeedleBundle\Sort\SortInterface;

/**
 * A query builder for Elasticsearch, to adapt the abstract Needle query format.
 */
class ElasticSelectQueryBuilder
{
    use DedupeFilterQueryTrait;

    /**
     * @var QueryShapeBuilder
     */
    private $queryShapeBuilder;

    public function __construct()
    {
        $this->queryShapeBuilder = new QueryShapeBuilder();
    }

    public function buildElasticQueryFromGeneric(
        ResolvedSelectQueryInterface $genericQuery,
        QueryBuildOptions $options
    ): array {
        $query = [];

        //if there are boost query fields to apply, apply them within the query clause
        $boostQueryFields = $genericQuery->getBoostQueryFields();

        //generate query clause
        if ($genericQuery->hasSearchTerm()) {
            if (!empty($boostQueryFields)) {
                $boostLucenifier = new BoostLucenifier();
                $fieldRefs = [];
                foreach ($boostQueryFields as $boostQueryField) {
                    $fieldRefs[] = $boostLucenifier->lucenifyBoost($boostQueryField);
                }
                $fieldsData = [
                    'fields' => $fieldRefs,
                ];
            } else {
                $fieldsData = [];
            }

            $searchTerm = addcslashes(strval($genericQuery->getSearchTerm()), '+-&|!(){}[]^”~*?:\\/');

            if (!$options->useWildcardSearch()) {
                $matchClause = [
                    'multi_match' => array_merge(
                        [
                            'query' => $searchTerm,
                        ],
                        $fieldsData
                    ),
                ];
            } else {
                $matchClause = [
                    'query_string' => array_merge(
                        [
                            'query' => sprintf('(%s) OR (*%s*)', trim($searchTerm, '*'), trim($searchTerm, '*')),
                        ],
                        $fieldsData
                    ),
                ];
            }
        } else {
            $matchClause = [
                'match_all' => new \stdClass(),
            ];
        }

        //determine whether we are using the facet values for an underlying "base" (recorded) query
        $shouldUseFacetValuesForRecordedQuery = $genericQuery->shouldUseFacetValuesForRecordedQuery();

        $filterQueries = $genericQuery->getFilterQueries();
        $baseQueries = $filterQueries;
        $extraQueries = [];
        if ($shouldUseFacetValuesForRecordedQuery) {
            $record = $genericQuery->getRecord();
            $recordedQueries = ($record) ? $record->getFilterQueries() : [];
            $baseQueries = $recordedQueries;
            $extraQueries = $this->diffQuerySets($filterQueries, $recordedQueries);
        }

        //if there are base filter queries, add them
        if (count($baseQueries) > 0) {
            $baseQueries = $this->dedupeFilterQueries($baseQueries);

            $mustClause = [$matchClause];
            foreach ($baseQueries as $filterQuery) {
                /** FilterQueryInterface $filterQuery */
                $clause = $this->queryShapeBuilder->getQueryShapeForFilterQuery($filterQuery);
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
            $facetNamesToExclude = $genericQuery->getFacetsToExclude();
            $shouldIncludeFacetValuesForMissing = $genericQuery->shouldRequestFacetValueForMissing();
            foreach ($facets as $facet) {
                //if query indicates we should skip this facet, then skip it
                if (false !== array_search($facet->getSearchKey(), $facetNamesToExclude)) {
                    continue;
                }
                $termsQuery = [
                    'terms' => [
                        'field' => $facet->getSearchKey(['prefer_parsed' => false]),
                        'min_doc_count' => ($shouldIncludeFacetValuesForMissing) ? 0 : 1,
                        'order' => ($genericQuery->getSortOrderForFacet($facet) === SortOrderProviderInterface::SORT_BY_COUNT)
                            ? ['_count' => 'asc']
                            : ['_key' => 'asc']
                    ],
                ];
                $facetFilters = $this->filterQueriesBySearchKey(
                    $extraQueries,
                    $facet->getSearchKey(['prefer_parsed' => false])
                );
                if (count($facetFilters) === 0) {
                    $query['aggs'][$facet->getName()] = $termsQuery;
                } else {
                    $query['aggs'][$facet->getName()] = [
                        'filter' => $this->formClauseForFilterQueries($facetFilters),
                        'aggs' => [
                            $facet->getName() => $termsQuery,
                        ],
                    ];
                }
            }
        }

        //if there are sorts to apply, apply them
        $sortCollection = $genericQuery->getSortCollection();
        if ($sortCollection instanceof SortCollectionInterface) {
            $query['sort'] = $sortCollection
                ->map(function (SortInterface $sort) {
                    try {
                        $sortKey = $sort->getFilter()->getSearchKey(['prefer_parsed' => false]);
                    } catch (UnformableSearchKeyException $e) {
                        return null;
                    }

                    return (object) [
                        $sortKey => ($sort->isDescending()) ? 'desc' : 'asc'
                    ];
                })
                ->filter(function ($v) {
                    return $v !== null;
                })
                ->toArray();
        }

        //if there is a post-filter to apply, apply it
        if (count($extraQueries) > 0) {
            $query['post_filter'] = $this->formClauseForFilterQueries($extraQueries);
        }

        return $query;
    }

    private function diffQuerySets(array $leftQueries, array $rightQueries): array
    {
        return array_filter(
            $leftQueries,
            function (FilterQueryInterface $query) use ($rightQueries) {
                return !$this->hasQueryWithinSet($query, $rightQueries);
            }
        );
    }

    private function hasQueryWithinSet(FilterQueryInterface $query, array $group): bool
    {
        foreach ($group as $groupQuery) {
            if ($this->hasSameQueries($query, $groupQuery)) {
                return true;
            }
        }

        return false;
    }

    private function hasSameQueries(FilterQueryInterface $leftQuery, FilterQueryInterface $rightQuery): bool
    {
        return $leftQuery->getValueType() === $rightQuery->getValueType()
            && $leftQuery->getSearchKey() === $rightQuery->getSearchKey()
            && $leftQuery->getSearchValue() === $rightQuery->getSearchValue();
    }

    private function filterQueriesBySearchKey(array $filterQueries, string $searchKey): array
    {
        return array_filter(
            $filterQueries,
            function (FilterQueryInterface $filterQuery) use ($searchKey) {
                return $filterQuery->getSearchKey() !== $searchKey;
            }
        );
    }

    private function formClauseForFilterQueries(array $filterQueries): ?array
    {
        if (count($filterQueries) === 1) {
            /** @var FilterQueryInterface $filterQuery */
            $filterQuery = array_values($filterQueries)[0];

            return $this->queryShapeBuilder->getQueryShapeForFilterQuery($filterQuery);
        }

        return [
            'bool' => [
                'must' => array_filter(array_map(
                    function (?FilterQueryInterface $filterQuery) {
                        if ($filterQuery === null) {
                            return null;
                        }

                        return $this->queryShapeBuilder->getQueryShapeForFilterQuery($filterQuery);
                    },
                    array_values($filterQueries)
                )),
            ],
        ];
    }
}
