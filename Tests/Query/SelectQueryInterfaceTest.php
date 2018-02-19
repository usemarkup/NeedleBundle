<?php

namespace Markup\NeedleBundle\Tests\Query;

use Markup\NeedleBundle\Query\SelectQueryInterface;
use PHPUnit\Framework\TestCase;

/**
* A test for a select query interface.
*/
class SelectQueryInterfaceTest extends TestCase
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
            'getGroupingField',
            'getGroupingSortCollection',
        ];
        $query = new \ReflectionClass(SelectQueryInterface::class);
        $actual_public_methods = [];
        foreach ($query->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $actual_public_methods[] = $method->name;
        }
        sort($actual_public_methods);
        sort($expected_public_methods);
        $this->assertEquals($expected_public_methods, $actual_public_methods);
    }
}
