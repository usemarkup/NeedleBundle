<?php

namespace Markup\NeedleBundle\Tests\Filter;

use Markup\NeedleBundle\Filter\IntersectionFilterValue;

/**
* A test for a filter value that represents the intersection of other values.
*/
class IntersectionFilterValueTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->filterValue1 = $this->getMock('Markup\NeedleBundle\Filter\FilterValueInterface');
        $this->filterValue2 = $this->getMock('Markup\NeedleBundle\Filter\FilterValueInterface');
        $this->intersectionValue = new IntersectionFilterValue(array($this->filterValue1, $this->filterValue2));
    }

    public function testIsIntersectionFilter()
    {
        $this->assertTrue($this->intersectionValue instanceof \Markup\NeedleBundle\Filter\IntersectionFilterValueInterface);
    }

    public function testSearchKeyForTwoFilterValues()
    {
        $this->filterValue1
            ->expects($this->any())
            ->method('getSearchValue')
            ->will($this->returnValue('filter1'));
        $this->filterValue2
            ->expects($this->any())
            ->method('getSearchValue')
            ->will($this->returnValue('filter2'));
        $this->assertEquals('(filter1 filter2)', $this->intersectionValue->getSearchValue());
    }

    public function testSearchKeyForOneFilterValue()
    {
        $this->filterValue1
            ->expects($this->any())
            ->method('getSearchValue')
            ->will($this->returnValue('filter1'));
        $intersectionValue = new IntersectionFilterValue(array($this->filterValue1));
        $this->assertEquals('filter1', $intersectionValue->getSearchValue());
    }

    public function testGetSlug()
    {
        $this->filterValue1
            ->expects($this->any())
            ->method('getSearchValue')
            ->will($this->returnValue('filter1'));
        $this->filterValue2
            ->expects($this->any())
            ->method('getSearchValue')
            ->will($this->returnValue('filter2'));
        $this->assertEquals('filter1::filter2', $this->intersectionValue->getSlug());
    }

    public function testGetValues()
    {
        $this->assertEquals(array($this->filterValue1, $this->filterValue2), $this->intersectionValue->getValues());
    }

    public function testIterateReturnsScalarFilters()
    {
        $this->filterValue1
            ->expects($this->any())
            ->method('getSearchValue')
            ->will($this->returnValue('filter1'));
        $this->filterValue2
            ->expects($this->any())
            ->method('getSearchValue')
            ->will($this->returnValue('filter2'));
        $iterated = array();
        foreach ($this->intersectionValue as $filterValue) {
            $iterated[] = $filterValue;
        }
        $this->assertContainsOnly('Markup\NeedleBundle\Filter\FilterValueInterface', $iterated);
        $this->assertCount(2, $iterated);
        $this->assertEquals(array('filter1', 'filter2'), array_map(function($filterValue) { return $filterValue->getSearchValue(); }, $iterated));
    }

    public function testAddFilterValue()
    {
        $filterValue3 = $this->getMock('Markup\NeedleBundle\Filter\FilterValueInterface');
        $this->intersectionValue->addFilterValue($filterValue3);
        $this->assertEquals(array($this->filterValue1, $this->filterValue2, $filterValue3), $this->intersectionValue->getValues());
    }
}
