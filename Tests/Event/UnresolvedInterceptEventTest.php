<?php

namespace Markup\NeedleBundle\Tests\Event;

use Markup\NeedleBundle\Event\UnresolvedInterceptEvent;

/**
* A test for an event that represents an unresolved intercept.
*/
class UnresolvedInterceptEventTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->definition = $this->createMock('Markup\NeedleBundle\Intercept\DefinitionInterface');
        $this->queryString = 'query';
        $this->message = 'Something could not be found!';
        $this->event = new UnresolvedInterceptEvent($this->definition, $this->queryString, $this->message);
    }

    public function testIsEvent()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\Event', $this->event);
    }

    public function testGetInterceptDefinition()
    {
        $this->assertSame($this->definition, $this->event->getInterceptDefinition());
    }

    public function testGetQueryString()
    {
        $this->assertEquals($this->queryString, $this->event->getQueryString());
    }

    public function testGetExceptionMessage()
    {
        $this->assertEquals($this->message, $this->event->getExceptionMessage());
    }
}
