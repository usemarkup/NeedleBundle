<?php

namespace Markup\NeedleBundle\Tests\Filter;

use Markup\NeedleBundle\Filter\ScalarFilterValue;

/**
* A test for a scalar filter value.
*/
class ScalarFilterValueTest extends \PHPUnit_Framework_TestCase
{
    public function testIsFilterValue()
    {
        $scalarFilterValue = new \ReflectionClass('Markup\NeedleBundle\Filter\ScalarFilterValue');
        $this->assertTrue($scalarFilterValue->implementsInterface('Markup\NeedleBundle\Filter\FilterValueInterface'));
    }

    public function testGetSearchValue()
    {
        $value = 8;
        $scalarValue = new ScalarFilterValue($value);
        $this->assertEquals($value, $scalarValue->getSearchValue());
        $this->assertInternalType(gettype($value), $scalarValue->getSearchValue());
    }

    public function testGetSlug()
    {
        $value = 8;
        $scalarValue = new ScalarFilterValue($value);
        $this->assertEquals($value, $scalarValue->getSlug());
        $this->assertInternalType('string', $scalarValue->getSlug());
    }
}
