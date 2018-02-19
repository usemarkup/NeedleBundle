<?php

namespace Markup\NeedleBundle\Tests\Filter;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Attribute\BooleanAttributeDecorator;
use Markup\NeedleBundle\Attribute\BooleanAttributeInterface;
use PHPUnit\Framework\TestCase;

/**
* A test for a decorator for a filter that declares a Boolean type (clocking any underlying type).
*/
class BooleanAttributeDecoratorTest extends TestCase
{
    /**
     * @var AttributeInterface
     */
    private $filter;

    /**
     * @var BooleanAttributeDecorator
     */
    private $decorator;

    protected function setUp()
    {
        $this->filter = $this->createMock(AttributeInterface::class);
        $this->decorator = new BooleanAttributeDecorator($this->filter);
    }

    public function testIsBooleanAttribute()
    {
        $this->assertInstanceOf(BooleanAttributeInterface::class, $this->decorator);
    }

    public function testIsAttribute()
    {
        $this->assertInstanceOf(AttributeInterface::class, $this->decorator);
    }

    public function testOneToOneDecoration()
    {
        $name = 'filter';
        $displayName = 'Filter';
        $searchKey = 'fil_ter';
        $this->filter
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));
        $this->filter
            ->expects($this->any())
            ->method('getDisplayName')
            ->will($this->returnValue($displayName));
        $this->filter
            ->expects($this->any())
            ->method('getSearchKey')
            ->will($this->returnValue($searchKey));
        $this->assertEquals($name, $this->decorator->getName());
        $this->assertEquals($displayName, $this->decorator->getDisplayName());
        $this->assertEquals($searchKey, $this->decorator->getSearchKey());
    }
}
