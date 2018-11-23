<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Builder;

use Markup\NeedleBundle\Attribute\AttributeInterface;
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
                $mustClause[] = [
                    'term' => [
                        $filterQuery->getSearchKey() => $filterQuery->getSearchValue(),
                    ],
                ];
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
                    ],
                ];
            }
        }

        return $query;
    }
}
