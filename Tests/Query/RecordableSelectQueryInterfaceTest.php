<?php

namespace Markup\NeedleBundle\Tests\Query;

use Markup\NeedleBundle\Query\RecordableSelectQueryInterface;
use PHPUnit\Framework\TestCase;

/**
* A test for a recordable select query interface.
*/
class RecordableSelectQueryInterfaceTest extends TestCase
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
        $actualPublicMethods = [];
        foreach ($query->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $actualPublicMethods[] = $method->name;
        }

        sort($actualPublicMethods);
        sort($expectedPublicMethods);
        $this->assertEquals($expectedPublicMethods, $actualPublicMethods);
    }
}
