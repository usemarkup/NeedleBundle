<?php

namespace Markup\NeedleBundle\Tests\Attribute;

use Markup\NeedleBundle\Attribute\Attribute;

/**
 * Test for filter implementation
 */
class AttributeTest extends \PHPUnit_Framework_TestCase
{
    public function testIsFilter()
    {
        $attr = new Attribute('name');
        $this->assertInstanceOf('Markup\NeedleBundle\Attribute\AttributeInterface', $attr);
    }

    public function testIsAttribute()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Attribute\AttributeInterface', new Attribute('name'));
    }

    public function testProvidesValueDisplayStrategy()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Attribute\AttributeProvidesValueDisplayStrategyInterface', new Attribute('name'));
    }

    public function testOutputsForOneWordName()
    {
        $name = 'filter';
        $attr = new Attribute($name);
        $this->assertEquals('filter', $attr->getName());
        $this->assertEquals('Filter', $attr->getDisplayName());
        $this->assertEquals('filter', $attr->getSearchKey());
    }

    public function testOutputsForTwoWordName()
    {
        $name = 'sleeve_length';
        $attr = new Attribute($name);
        $this->assertEquals('sleeve_length', $attr->getName());
        $this->assertEquals('Sleeve length', $attr->getDisplayName());
        $this->assertEquals('sleeve_length', $attr->getSearchKey());
    }

    public function testOutputsForDifferentNameAndKey()
    {
        $name = 'category';
        $key = 'category_key';
        $attr = new Attribute($name, $key);
        $this->assertEquals($name, $attr->getName());
        $this->assertEquals('Category', $attr->getDisplayName());
        $this->assertEquals($key, $attr->getSearchKey());
    }

    public function testOutputsWithAllSpecified()
    {
        $name = 'name';
        $key = 'key';
        $display = 'display';
        $attr = new Attribute($name, $key, $display);
        $this->assertEquals($name, $attr->getName());
        $this->assertEquals($display, $attr->getDisplayName());
        $this->assertEquals($key, $attr->getSearchKey());
    }

    public function testGetViewDisplayStrategyReturnsDefault()
    {
        $name = 'sleeve_length';
        $attr = new Attribute($name);
        $value = 'value';
        $strategy = $attr->getValueDisplayStrategy();
        $this->assertInstanceOf('Closure', $strategy);
        $this->assertEquals($value, call_user_func($strategy, $value));
    }
}
