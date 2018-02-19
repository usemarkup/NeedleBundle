<?php

namespace Markup\NeedleBundle\Tests\Filter;

use Markup\NeedleBundle\Filter\FilterValueInterface;
use Markup\NeedleBundle\Filter\IntersectionFilterValue;
use Markup\NeedleBundle\Filter\IntersectionFilterValueInterface;
use PHPUnit\Framework\TestCase;

/**
* A test for a filter value that represents the intersection of other values.
*/
class IntersectionFilterValueTest extends TestCase
{
    /**
     * @var FilterValueInterface
     */
    private $filterValue1;

    /**
     * @var FilterValueInterface
     */
    private $filterValue2;

    /**
     * @var IntersectionFilterValue
     */
    private $intersectionValue;

    protected function setUp()
    {
        $this->filterValue1 = $this->createMock(FilterValueInterface::class);
        $this->filterValue2 = $this->createMock(FilterValueInterface::class);
        $this->intersectionValue = new IntersectionFilterValue([$this->filterValue1, $this->filterValue2]);
    }

    public function testIsIntersectionFilter()
    {
        $this->assertInstanceOf(IntersectionFilterValueInterface::class, $this->intersectionValue);
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
        $intersectionValue = new IntersectionFilterValue([$this->filterValue1]);
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
        $this->assertEquals([$this->filterValue1, $this->filterValue2], $this->intersectionValue->getValues());
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
        foreach ($this->intersectionValue as $filterValue) {
            $iterated[] = $filterValue;
        }
        $this->assertContainsOnly(FilterValueInterface::class, $iterated);
        $this->assertCount(2, $iterated);
        $this->assertEquals(['filter1', 'filter2'], array_map(function($filterValue) { return $filterValue->getSearchValue(); }, $iterated));
    }

    public function testAddFilterValue()
    {
        $filterValue3 = $this->createMock(FilterValueInterface::class);
        $this->intersectionValue->addFilterValue($filterValue3);
        $this->assertEquals([$this->filterValue1, $this->filterValue2, $filterValue3], $this->intersectionValue->getValues());
    }
}
