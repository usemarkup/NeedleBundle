<?php

namespace Markup\NeedleBundle\Tests\Lucene;

use Markup\NeedleBundle\Filter;
use Markup\NeedleBundle\Filter\FilterValueInterface;
use Markup\NeedleBundle\Lucene\FilterValueLucenifier;
use Markup\NeedleBundle\Lucene\Helper;
use PHPUnit\Framework\TestCase;

/**
* A test for an object that can turn a filter value into a Lucene expression.
*/
class FilterValueLucenifierTest extends TestCase
{
    public function setUp()
    {
        if (!class_exists(Helper::class)) {
            $this->fail('The Solarium library (3.x) needs to be included.');
        }
        $this->helper = new Helper(new \Solarium\Core\Query\Helper()); //make a real instance
        $this->lucenifier = new FilterValueLucenifier($this->helper);
    }

    public function testSimpleCase()
    {
        $filterValue = $this->createMock(FilterValueInterface::class);
        $filterValue
            ->expects($this->any())
            ->method('getSearchValue')
            ->will($this->returnValue('red'));
        $expectedExpr = '"red"';
        $this->assertEquals($expectedExpr, $this->lucenifier->lucenify($filterValue));
    }

    public function testUnionOfScalars()
    {
        $value1 = 'red';
        $value2 = 'blue';
        $expectedExpr = '("red" "blue")';
        $scalar1 = new Filter\ScalarFilterValue($value1);
        $scalar2 = new Filter\ScalarFilterValue($value2);
        $union = new Filter\UnionFilterValue([$scalar1, $scalar2]);
        $this->assertEquals($expectedExpr, $this->lucenifier->lucenify($union));
    }

    public function testIntersectionOfScalars()
    {
        $value1 = 'red';
        $value2 = 'blue';
        $expectedExpr = '(+"red" +"blue")';
        $scalar1 = new Filter\ScalarFilterValue($value1);
        $scalar2 = new Filter\ScalarFilterValue($value2);
        $intersection = new Filter\IntersectionFilterValue([$scalar1, $scalar2]);
        $this->assertEquals($expectedExpr, $this->lucenifier->lucenify($intersection));
    }

    public function testRangeFilterValue()
    {
        $min = 50;
        $max = 100;
        $expectedExpr = '[50 TO 100]';
        $range = new Filter\RangeFilterValue($min, $max);
        $this->assertEquals($expectedExpr, $this->lucenifier->lucenify($range));
    }

    public function testIntersectionOfUnions()
    {
        $value1 = 'green';
        $value2 = 'grey';
        $value3 = 'blue';
        $value4 = 'red';
        $expectedExpr = '(+("green" "grey") +("blue" "red"))';
        $scalar1 = new Filter\ScalarFilterValue($value1);
        $scalar2 = new Filter\ScalarFilterValue($value2);
        $scalar3 = new Filter\ScalarFilterValue($value3);
        $scalar4 = new Filter\ScalarFilterValue($value4);
        $union1 = new Filter\UnionFilterValue([$scalar1, $scalar2]);
        $union2 = new Filter\UnionFilterValue([$scalar3, $scalar4]);
        $intersection = new Filter\IntersectionFilterValue([$union1, $union2]);
        $this->assertEquals($expectedExpr, $this->lucenifier->lucenify($intersection));
    }
}
