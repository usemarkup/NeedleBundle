<?php

namespace Markup\NeedleBundle\Tests\Query;

use Markup\NeedleBundle\Query\ResolvedSelectQueryInterface;
use PHPUnit\Framework\TestCase;

/**
 * A test for a select query interface.
 */
class ResolvedSelectQueryInterfaceTest extends TestCase
{
    public function testHasCorrectPublicMethods()
    {
        $expectedPublicMethods = [
            'getFilterQueries',
            'getFields',
            'getPageNumber',
            'hasSearchTerm',
            'getSearchTerm',
            'getSearchContext',
            'getSortCollection',
            'getFacetsToExclude',
            'getFilterQueryWithKey',
            'doesValueExistInFilterQueries',
            'getMaxPerPage',
            'shouldTreatAsTextSearch',
            'getFacetCollatorProvider',
            'getFacets',
            'getRecord',
            'shouldRequestFacetValueForMissing',
            'getSortOrderForFacet',
            'getWhetherFacetIgnoresCurrentFilters',
            'getBoostQueryFields',
            'shouldUseFacetValuesForRecordedQuery',
            'getOriginalSelectQuery',
            'getGroupingField',
            'getGroupingSortCollection',
            'shouldUseFuzzyMatching',
        ];
        $query = new \ReflectionClass(ResolvedSelectQueryInterface::class);
        $actualPublicMethods = [];
        foreach ($query->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $actualPublicMethods[] = $method->name;
        }
        sort($actualPublicMethods);
        sort($expectedPublicMethods);
        $this->assertEquals($expectedPublicMethods, $actualPublicMethods);
    }
}
