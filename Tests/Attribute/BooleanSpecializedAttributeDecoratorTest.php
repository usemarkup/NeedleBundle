<?php

namespace Markup\NeedleBundle\Tests\Filter;

use Markup\NeedleBundle\Attribute\BooleanAttributeInterface;
use Markup\NeedleBundle\Attribute\BooleanSpecializedAttributeDecorator;
use Markup\NeedleBundle\Attribute\SpecializedAttributeInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
* A test for a decorator for a filter that declares a Boolean type (clocking any underlying type).
*/
class BooleanSpecializedAttributeDecoratorTest extends MockeryTestCase
{
    /**
     * @var SpecializedAttributeInterface|m\Mock
     */
    private $filter;

    /**
     * @var BooleanSpecializedAttributeDecorator
     */
    private $decorator;


    protected function setUp()
    {
        $this->filter = m::mock(SpecializedAttributeInterface::class);
        $this->decorator = new BooleanSpecializedAttributeDecorator($this->filter);
    }

    public function testIsSpecializedAttribute()
    {
        $this->assertInstanceOf(SpecializedAttributeInterface::class, $this->decorator);
    }

    public function testIsBooleanAttribute()
    {
        $this->assertInstanceOf(BooleanAttributeInterface::class, $this->decorator);
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
