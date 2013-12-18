<?php

namespace Markup\NeedleBundle\Tests\Result;

use Markup\NeedleBundle\Tests\AbstractInterfaceTestCase;

/**
* A test for a search result interface.
*/
class ResultInterfaceTest extends AbstractInterfaceTestCase
{
    public function testIsCountable()
    {
        $result = new \ReflectionClass('Markup\NeedleBundle\Result\ResultInterface');
        $this->assertTrue($result->implementsInterface('Countable'));
    }

    public function testIsTraversable()
    {
        $result = new \ReflectionClass('Markup\NeedleBundle\Result\ResultInterface');
        $this->assertTrue($result->implementsInterface('Traversable'));
    }

    protected function getExpectedPublicMethods()
    {
        return array(
            'getTotalCount',
            'getQueryTimeInMilliseconds',
            'count',
            'getIterator',
            'getTotalPageCount',
            'getCurrentPageNumber',
            'isPaginated',
            'hasPreviousPage',
            'getPreviousPageNumber',
            'hasNextPage',
            'getNextPageNumber',
            'getFacetSets',
            'hasDebugOutput',
            'getDebugOutput',
            );
    }

    protected function getInterfaceUnderTest()
    {
        return 'Markup\NeedleBundle\Result\ResultInterface';
    }
}
