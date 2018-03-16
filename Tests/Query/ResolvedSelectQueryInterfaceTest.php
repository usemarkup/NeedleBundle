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
            'hasFilterQueries',
            'getFields',
            'getPageNumber',
            'hasSearchTerm',
            'getSearchTerm',
            'getSortCollection',
            'hasSortCollection',
            'getFacetNamesToExclude',
            'getResult',
            'getResultAsync',
            'setSearchService',
            'getFilterQueryWithKey',
            'doesValueExistInFilterQueries',
            'getMaxPerPage',
            'shouldTreatAsTextSearch',
            'getSpellcheck',
            'getFacets',
            'shouldRequestFacetValueForMissing',
            'getSortOrderForFacet',
            'getWhetherFacetIgnoresCurrentFilters',
            'getBoostQueryFields',
            'shouldUseFacetValuesForRecordedQuery',
            'getOriginalSelectQuery',
            'getRecord',
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
