<?php

namespace Markup\NeedleBundle\Tests\Filter;

use Markup\NeedleBundle\Filter\UnionFilterValue;

/**
* A test for a filter value that represents the union of other values.
*/
class UnionFilterValueTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->filterValue1 = $this->getMock('Markup\NeedleBundle\Filter\FilterValueInterface');
        $this->filterValue2 = $this->getMock('Markup\NeedleBundle\Filter\FilterValueInterface');
        $this->unionValue = new UnionFilterValue([$this->filterValue1, $this->filterValue2]);
    }

    public function testIsUnionFilter()
    {
        $this->assertTrue($this->unionValue instanceof \Markup\NeedleBundle\Filter\UnionFilterValueInterface);
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
        $this->assertEquals('(filter1 filter2)', $this->unionValue->getSearchValue());
    }

    public function testSearchKeyForOneFilterValue()
    {
        $this->filterValue1
            ->expects($this->any())
            ->method('getSearchValue')
            ->will($this->returnValue('filter1'));
        $unionValue = new UnionFilterValue([$this->filterValue1]);
        $this->assertEquals('filter1', $unionValue->getSearchValue());
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
        $this->assertEquals('filter1::filter2', $this->unionValue->getSlug());
    }

    public function testGetValues()
    {
        $this->assertEquals([$this->filterValue1, $this->filterValue2], $this->unionValue->getValues());
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
        $iterated = [];
        foreach ($this->unionValue as $filterValue) {
            $iterated[] = $filterValue;
        }
        $this->assertContainsOnly('Markup\NeedleBundle\Filter\FilterValueInterface', $iterated);
        $this->assertCount(2, $iterated);
        $this->assertEquals(['filter1', 'filter2'], array_map(function($filterValue) { return $filterValue->getSearchValue(); }, $iterated));
    }

    public function testAddFilterValue()
    {
        $filterValue3 = $this->getMock('Markup\NeedleBundle\Filter\FilterValueInterface');
        $this->unionValue->addFilterValue($filterValue3);
        $this->assertEquals([$this->filterValue1, $this->filterValue2, $filterValue3], $this->unionValue->getValues());
    }
}
