<?php

namespace Markup\NeedleBundle\Tests\Filter;

use Markup\NeedleBundle\Filter\FilterValueInterface;
use Markup\NeedleBundle\Filter\ScalarFilterValue;
use PHPUnit\Framework\TestCase;

/**
* A test for a scalar filter value.
*/
class ScalarFilterValueTest extends TestCase
{
    public function testIsFilterValue()
    {
        $scalarFilterValue = new \ReflectionClass(ScalarFilterValue::class);
        $this->assertTrue($scalarFilterValue->implementsInterface(FilterValueInterface::class));
    }

    public function testGetSearchValue()
    {
        $value = 8;
        $scalarValue = new ScalarFilterValue($value);
        $this->assertEquals($value, $scalarValue->getSearchValue());
        $this->assertIsInt($scalarValue->getSearchValue());
    }

    public function testGetSlug()
    {
        $value = 8;
        $scalarValue = new ScalarFilterValue($value);
        $this->assertEquals($value, $scalarValue->getSlug());
        $this->assertIsString($scalarValue->getSlug());
    }
}
