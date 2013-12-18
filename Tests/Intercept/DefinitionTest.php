<?php

namespace Markup\NeedleBundle\Tests\Intercept;

use Markup\NeedleBundle\Intercept\Definition;

/**
* Test for an intercept definition.
*/
class DefinitionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->matcher = $this->getMock('Markup\NeedleBundle\Intercept\MatcherInterface');
        $this->type = 'type';
        $this->name = 'definition';
        $this->properties = array('this' => 'that');
        $this->definition = new Definition($this->name, $this->matcher, $this->type, $this->properties);
    }

    public function testIsDefinition()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Intercept\DefinitionInterface', $this->definition);
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
