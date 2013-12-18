<?php

namespace Markup\NeedleBundle\Tests\Context;

use Markup\NeedleBundle\Context\RemoveDefaultFilterQueriesContextDecorator;

/**
* A test for a search context decorator that has no default filter queries.
*/
class RemoveDefaultFilterQueriesContextDecoratorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->context = $this->getMock('Markup\NeedleBundle\Context\SearchContextInterface');
        $this->decorator = new RemoveDefaultFilterQueriesContextDecorator($this->context);
    }

    public function testIsSearchContext()
    {
        $this->assertTrue($this->decorator instanceof \Markup\NeedleBundle\Context\SearchContextInterface);
    }

    public function testGetDefaultFilterQueriesIgnoresUnderlyingQueries()
    {
        $filterQuery = $this->getMock('Markup\NeedleBundle\Filter\FilterQueryInterface');
        $this->context
            ->expects($this->any())
            ->method('getDefaultFilterQueries')
            ->will($this->returnValue(array($filterQuery)));
        $this->assertEquals(array(), $this->decorator->getDefaultFilterQueries());
    }

    public function testGetItemsPerPageReturnsLargeNumber()
    {
        $this->assertEquals(RemoveDefaultFilterQueriesContextDecorator::LARGE_NUMBER, $this->decorator->getItemsPerPage());
    }

    //skipping other decorations as unlikely to regress
}
