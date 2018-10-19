<?php

namespace Markup\NeedleBundle\Tests\Filter;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Attribute\FloatAttributeInterface;
use Markup\NeedleBundle\Attribute\FloatSpecializedAttributeDecorator;
use Markup\NeedleBundle\Attribute\SpecializedAttributeInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
* A test for a decorator for a filter that declares a Boolean type (clocking any underlying type).
*/
class FloatSpecializedAttributeDecoratorTest extends MockeryTestCase
{
    /**
     * @var SpecializedAttributeInterface|m\Mock
     */
    private $filter;

    /**
     * @var FloatSpecializedAttributeDecorator
     */
    private $decorator;

    protected function setUp()
    {
        $this->filter = m::mock(implode(',', [SpecializedAttributeInterface::class, AttributeInterface::class]));
        $this->decorator = new FloatSpecializedAttributeDecorator($this->filter);
    }

    public function testIsSpecializedAttribute()
    {
        $this->assertInstanceOf(SpecializedAttributeInterface::class, $this->decorator);
    }

    public function testIsBooleanAttribute()
    {
        $this->assertInstanceOf(FloatAttributeInterface::class, $this->decorator);
    }

    public function testOneToOneDecoration()
    {
        $name = 'filter';
        $displayName = 'Filter';
        $searchKey = 'fil_ter';
        $this->filter
            ->shouldReceive('getName')
            ->andReturn($name);

        $this->filter
            ->shouldReceive('getDisplayName')
            ->andReturn($displayName);

        $this->filter
            ->shouldReceive('getSearchKey')
            ->andReturn($searchKey);

        $this->assertEquals($name, $this->decorator->getName());
        $this->assertEquals($displayName, $this->decorator->getDisplayName());
        $this->assertEquals($searchKey, $this->decorator->getSearchKey());
    }
}
