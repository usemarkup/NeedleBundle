<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Builder;

use Markup\NeedleBundle\Filter;

trait DedupeFilterQueryTrait
{
    /**
     * Takes a collection of filter queries, and checks there is no more than one filter query per filter name.  If multiple filter queries are found against an individual filter name, they are combined together into an intersection.
     *
     * @return Filter\FilterQueryInterface[]
     **/
    private function dedupeFilterQueries($filterQueries)
    {
        $nameCounts = [];
        foreach ($filterQueries as $filterQuery) {
            $name = $filterQuery->getSearchKey();
            if (!isset($nameCounts[$name])) {
                $nameCounts[$name] = 1;
            } else {
                $nameCounts[$name]++;
            }
        }

        //if there are no dupes, just return the original queries
        if (array_values(array_unique($nameCounts)) == [1]) {
            return $filterQueries;
        }

        $namesToProcess = array_keys(array_filter($nameCounts, function ($v) {
            return $v > 1;
        }));
        $intersectFilterQueries = [];
        $intersectibleQueries = [];
        $filters = [];
        foreach ($filterQueries as $filterQuery) {
            if (!in_array($filterQuery->getSearchKey(), $namesToProcess)) {
                continue;
            }
            $filters[$filterQuery->getSearchKey()] = $filterQuery->getFilter();
        }
        foreach ($namesToProcess as $nameToProcess) {
            $intersectibleQueries[$nameToProcess] = [];
            foreach ($filterQueries as $filterQuery) {
                if ($filterQuery->getSearchKey() === $nameToProcess) {
                    $intersectibleQueries[$nameToProcess][] = $filterQuery;
                }
            }
        }
        foreach ($intersectibleQueries as $key => $querySet) {
            $intersectFilterValue = new Filter\IntersectionFilterValue([]);
            foreach ($querySet as $query) {
                if ($query->getFilterValue() instanceof Filter\IntersectionFilterValueInterface) {
                    foreach ($query->getFilterValue() as $filterValue) {
                        $intersectFilterValue->addFilterValue($filterValue);
                    }
                } else {
                    $intersectFilterValue->addFilterValue($query->getFilterValue());
                }
            }
            $intersectFilterQueries[] = new Filter\FilterQuery($filters[$key], $intersectFilterValue);
        }

        $dedupedFilterQueries = $intersectFilterQueries;
        foreach ($filterQueries as $filterQuery) {
            if (!in_array($filterQuery->getSearchKey(), $namesToProcess)) {
                $dedupedFilterQueries[] = $filterQuery;
            }
        }

        return $dedupedFilterQueries;
    }
}
