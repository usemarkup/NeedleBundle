<?php

namespace Markup\NeedleBundle\Tests\Query;

/**
* A test for a recordable select query interface.
*/
class RecordableSelectQueryInterfaceTest extends \PHPUnit_Framework_TestCase
{
    public function testHasCorrectPublicMethods()
    {
        $expected_public_methods = array(
            'getFilterQueries',
            'hasFilterQueries',
            'getPageNumber',
            'hasSearchTerm',
            'getSearchTerm',
            'getSortCollection',
            'hasSortCollection',
            'getFacetNamesToExclude',
            'getResult',
            'setSearchService',
            'record',
            'getRecord',
            'hasRecord',
            'getFilterQueryWithKey',
            'doesValueExistInFilterQueries',
            'getMaxPerPage',
            'shouldTreatAsTextSearch',
            'getSpellcheck',
            );
        $query = new \ReflectionClass('Markup\NeedleBundle\Query\RecordableSelectQueryInterface');
        $actual_public_methods = array();
        foreach ($query->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $actual_public_methods[] = $method->name;
        }
        sort($actual_public_methods);
        sort($expected_public_methods);
        $this->assertEquals($expected_public_methods, $actual_public_methods);
    }
}
