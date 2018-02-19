<?php

namespace Markup\NeedleBundle\Tests\Context;

use Markup\NeedleBundle\Context\RemoveDefaultFilterQueriesContextDecorator;
use Markup\NeedleBundle\Context\SearchContextInterface;
use Markup\NeedleBundle\Filter\FilterQueryInterface;
use PHPUnit\Framework\TestCase;

/**
* A test for a search context decorator that has no default filter queries.
*/
class RemoveDefaultFilterQueriesContextDecoratorTest extends TestCase
{
    public function setUp()
    {
        $this->context = $this->createMock(SearchContextInterface::class);
        $this->decorator = new RemoveDefaultFilterQueriesContextDecorator($this->context);
    }

    public function testIsSearchContext()
    {
        $this->assertInstanceOf(SearchContextInterface::class, $this->decorator);
    }

    public function testGetDefaultFilterQueriesIgnoresUnderlyingQueries()
    {
        $filterQuery = $this->createMock(FilterQueryInterface::class);
        $this->context
            ->expects($this->any())
            ->method('getDefaultFilterQueries')
            ->will($this->returnValue([$filterQuery]));
        $this->assertEquals([], $this->decorator->getDefaultFilterQueries());
    }

    public function testGetItemsPerPageReturnsLargeNumber()
    {
        $this->assertEquals(RemoveDefaultFilterQueriesContextDecorator::LARGE_NUMBER, $this->decorator->getItemsPerPage());
    }

    //skipping other decorations as unlikely to regress
}
