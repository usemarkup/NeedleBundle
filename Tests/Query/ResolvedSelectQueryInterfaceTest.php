<?php

namespace Markup\NeedleBundle\Tests\Query;

use Markup\NeedleBundle\Query\ResolvedSelectQueryInterface;

/**
 * A test for a select query interface.
 */
class ResolvedSelectQueryInterfaceTest extends \PHPUnit_Framework_TestCase
{
    public function testHasCorrectPublicMethods()
    {
        $expected_public_methods = [
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
            'getGroupingSortCollection'
        ];
        $query = new \ReflectionClass(ResolvedSelectQueryInterface::class);
        $actual_public_methods = [];
        foreach ($query->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $actual_public_methods[] = $method->name;
        }
        sort($actual_public_methods);
        sort($expected_public_methods);
        $this->assertEquals($expected_public_methods, $actual_public_methods);
    }
}
