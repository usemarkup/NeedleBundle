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
        $expectedPublicMethods = [
            'getFilterQueries',
            'hasFilterQueries',
            'getFields',
            'getPageNumber',
            'hasSearchTerm',
            'getSearchTerm',
            'getSortCollection',
            'hasSortCollection',
            'getFacetsToExclude',
            'getFilterQueryWithKey',
            'doesValueExistInFilterQueries',
            'getMaxPerPage',
            'shouldTreatAsTextSearch',
            'getSpellcheck',
            'getGroupingField',
            'getGroupingSortCollection',
        ];
        $query = new \ReflectionClass(SelectQueryInterface::class);
        $actualPublicMethods = [];
        foreach ($query->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $actualPublicMethods[] = $method->name;
        }
        sort($actualPublicMethods);
        sort($expectedPublicMethods);
        $this->assertEquals($expectedPublicMethods, $actualPublicMethods);
    }
}
