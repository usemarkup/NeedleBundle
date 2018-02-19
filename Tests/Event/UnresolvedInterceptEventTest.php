<?php

namespace Markup\NeedleBundle\Tests\Event;

use Markup\NeedleBundle\Event\UnresolvedInterceptEvent;
use Markup\NeedleBundle\Intercept\DefinitionInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\Event;

/**
* A test for an event that represents an unresolved intercept.
*/
class UnresolvedInterceptEventTest extends TestCase
{
    /**
     * @var UnresolvedInterceptEvent
     */
    private $event;

    protected function setUp()
    {
        $this->definition = $this->createMock(DefinitionInterface::class);
        $this->queryString = 'query';
        $this->message = 'Something could not be found!';
        $this->event = new UnresolvedInterceptEvent($this->definition, $this->queryString, $this->message);
    }

    public function testIsEvent()
    {
        $this->assertInstanceOf(Event::class, $this->event);
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
