<?php

namespace Markup\NeedleBundle\Tests\Result;

use Markup\NeedleBundle\Result\PagerfantaResultAdapter;

/**
* A test for a search result that adapts a Pagerfanta object.
*/
class PagerfantaResultAdapterResultTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->pagerfanta = $this->getMockBuilder('Pagerfanta\Pagerfanta')
            ->disableOriginalConstructor()
            ->getMock();
        $this->adapter = new PagerfantaResultAdapter($this->pagerfanta);
    }

    public function testIsResult()
    {
        $this->assertTrue($this->adapter instanceof \Markup\NeedleBundle\Result\ResultInterface);
    }

    public function testGetTotalCount()
    {
        $count = 42;
        $this->pagerfanta
            ->expects($this->any())
            ->method('getNbResults')
            ->will($this->returnValue($count));
        $this->assertEquals(42, $this->adapter->getTotalCount());
    }

    public function testGetIterator()
    {
        $resultIterator = new \ArrayIterator(['these', 'are', 'some', 'results']);
        $this->pagerfanta
            ->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue($resultIterator));
        $this->assertEquals($resultIterator, $this->adapter->getIterator());
    }

    public function testCount()
    {
        $count = 42;
        $this->pagerfanta
            ->expects($this->any())
            ->method('getNbResults')
            ->will($this->returnValue($count));
        $this->assertEquals(42, count($this->adapter));
    }

    public function testGetQueryTimeWithNoQueryTimeStrategySet()
    {
        $this->assertFalse($this->adapter->getQueryTimeInMilliseconds(), '->getQueryTimeInMilliseconds returns false when no query time strategy set');
    }

    public function testGetQueryTimeWithStrategy()
    {
        $time = 42.0;
        $strategy = $this->createMock('Markup\NeedleBundle\Result\QueryTimeStrategyInterface');
        $strategy
            ->expects($this->any())
            ->method('getQueryTimeInMilliseconds')
            ->will($this->returnValue($time));
        $this->adapter->setQueryTimeStrategy($strategy);
        $this->assertEquals($time, $this->adapter->getQueryTimeInMilliseconds());
    }

    public function testGetTotalPageCount()
    {
        $pages = 7;
        $this->pagerfanta
            ->expects($this->any())
            ->method('getNbPages')
            ->will($this->returnValue($pages));
        $this->assertEquals($pages, $this->adapter->getTotalPageCount());
    }

    public function testGetCurrentPageNumber()
    {
        $currentPage = 5;
        $this->pagerfanta
            ->expects($this->any())
            ->method('getCurrentPage')
            ->will($this->returnValue($currentPage));
        $this->assertEquals($currentPage, $this->adapter->getCurrentPageNumber());
    }

    public function testIsPaginated()
    {
        $whetherPaginated = true;
        $this->pagerfanta
            ->expects($this->any())
            ->method('haveToPaginate')
            ->will($this->returnValue($whetherPaginated));
        $this->assertSame($whetherPaginated, $this->adapter->isPaginated());
    }

    public function testHasPreviousPage()
    {
        $hasPrevious = true;
        $this->pagerfanta
            ->expects($this->atLeastOnce())
            ->method('hasPreviousPage')
            ->will($this->returnValue($hasPrevious));
        $this->assertSame($hasPrevious, $this->adapter->hasPreviousPage());
    }

    public function testGetPreviousPageIfExists()
    {
        $previous = 2;
        $this->pagerfanta
            ->expects($this->any())
            ->method('getPreviousPage')
            ->will($this->returnValue($previous));
        $this->assertSame($previous, $this->adapter->getPreviousPageNumber());
    }

    public function testGetPreviousPageIfDoesNotExist()
    {
        $this->pagerfanta
            ->expects($this->any())
            ->method('getPreviousPage')
            ->will($this->throwException(new \LogicException));
        $this->setExpectedException('Markup\NeedleBundle\Result\PageDoesNotExistException');
        $this->adapter->getPreviousPageNumber();
    }

    public function testHasNextPage()
    {
        $hasNext = true;
        $this->pagerfanta
            ->expects($this->atLeastOnce())
            ->method('hasNextPage')
            ->will($this->returnValue($hasNext));
        $this->assertSame($hasNext, $this->adapter->hasNextPage());
    }

    public function testGetNextPageIfExists()
    {
        $next = 2;
        $this->pagerfanta
            ->expects($this->any())
            ->method('getNextPage')
            ->will($this->returnValue($next));
        $this->assertSame($next, $this->adapter->getNextPageNumber());
    }

    public function testGetNextPageIfDoesNotExist()
    {
        $this->pagerfanta
            ->expects($this->any())
            ->method('getNextPage')
            ->will($this->throwException(new \LogicException));
        $this->setExpectedException('Markup\NeedleBundle\Result\PageDoesNotExistException');
        $this->adapter->getNextPageNumber();
    }

    public function testGetFacetSetsWithNoStrategySet()
    {
        $this->assertCount(0, $this->adapter->getFacetSets());
    }

    public function testGetFacetSetsWithStrategySet()
    {
        $facetSet = $this->createMock('Markup\NeedleBundle\Facet\FacetSetInterface');
        $facetSetStrategy = $this->createMock('Markup\NeedleBundle\Result\FacetSetStrategyInterface');
        $facetSetStrategy
            ->expects($this->any())
            ->method('getFacetSets')
            ->will($this->returnValue([$facetSet]));
        $this->adapter->setFacetSetStrategy($facetSetStrategy);
        $this->assertCount(1, $this->adapter->getFacetSets());
    }

    public function testDebugOutputWithNoStrategySet()
    {
        $this->assertFalse($this->adapter->hasDebugOutput());
        $this->assertNull($this->adapter->getDebugOutput());
    }

    public function testDebugOutputWithStrategySet()
    {
        $debugStrategy = $this->createMock('Markup\NeedleBundle\Result\DebugOutputStrategyInterface');
        $debugStrategy
            ->expects($this->any())
            ->method('hasDebugOutput')
            ->will($this->returnValue(true));
        $output = "debugging output!!";
        $debugStrategy
            ->expects($this->any())
            ->method('getDebugOutput')
            ->will($this->returnValue($output));
        $this->adapter->setDebugOutputStrategy($debugStrategy);
        $this->assertTrue($this->adapter->hasDebugOutput());
        $this->assertEquals($output, $this->adapter->getDebugOutput());
    }

    public function testGetPagerfanta()
    {
        $this->assertSame($this->pagerfanta, $this->adapter->getPagerfanta());
    }
}
