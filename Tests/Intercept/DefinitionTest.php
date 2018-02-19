<?php

namespace Markup\NeedleBundle\Tests\Intercept;

use Markup\NeedleBundle\Intercept\Definition;
use Markup\NeedleBundle\Intercept\DefinitionInterface;
use Markup\NeedleBundle\Intercept\MatcherInterface;
use PHPUnit\Framework\TestCase;

/**
* Test for an intercept definition.
*/
class DefinitionTest extends TestCase
{
    protected function setUp()
    {
        $this->matcher = $this->createMock(MatcherInterface::class);
        $this->type = 'type';
        $this->name = 'definition';
        $this->properties = ['this' => 'that'];
        $this->definition = new Definition($this->name, $this->matcher, $this->type, $this->properties);
    }

    public function testIsDefinition()
    {
        $this->assertInstanceOf(DefinitionInterface::class, $this->definition);
    }

    public function testGetMatcher()
    {
        $this->assertSame($this->matcher, $this->definition->getMatcher());
    }

    public function testGetName()
    {
        $this->assertEquals($this->name, $this->definition->getName()   );
    }

    public function testGetType()
    {
        $this->assertEquals($this->type, $this->definition->getType());
    }

    public function testGetProperties()
    {
        $this->assertEquals($this->properties, $this->definition->getProperties());
    }
}
