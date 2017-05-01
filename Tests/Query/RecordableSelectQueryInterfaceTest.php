<?php

namespace Markup\NeedleBundle\Tests\Query;

use Markup\NeedleBundle\Query\RecordableSelectQueryInterface;

/**
* A test for a recordable select query interface.
*/
class RecordableSelectQueryInterfaceTest extends \PHPUnit_Framework_TestCase
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
            'record',
            'getRecord',
            'hasRecord',
            'getFilterQueryWithKey',
            'doesValueExistInFilterQueries',
            'getMaxPerPage',
            'shouldTreatAsTextSearch',
            'getSpellcheck',
            'getGroupingField',
            'getGroupingSortCollection'
        ];
        $query = new \ReflectionClass(RecordableSelectQueryInterface::class);
        $actual_public_methods = [];
        foreach ($query->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $actual_public_methods[] = $method->name;
        }
        sort($actual_public_methods);
        sort($expected_public_methods);
        $this->assertEquals($expected_public_methods, $actual_public_methods);
    }
}
