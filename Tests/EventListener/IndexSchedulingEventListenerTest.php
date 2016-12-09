<?php

namespace Markup\NeedleBundle\Tests\EventListener;

use Markup\NeedleBundle\EventListener\IndexSchedulingEventListener;

/**
* A test for an event listener that schedules a search index.
*/
class IndexSchedulingEventListenerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->scheduler = $this->getMockBuilder('Markup\NeedleBundle\Scheduler\IndexScheduler')
            ->disableOriginalConstructor()
            ->getMock();
        $this->listener = new IndexSchedulingEventListener($this->scheduler);
    }

    public function testAddScheduledIndexWithAddedCorpora()
    {
        $corpus1 = 'corpus1';
        $corpus2 = 'corpus2';
        $eventName1 = 'event1';
        $eventName2 = 'event2';
        $event1 = $this->createMock('Symfony\Component\EventDispatcher\Event');
        $this->listener->addCorpusForEvent($corpus1, $eventName1);
        $this->listener->addCorpusForEvent($corpus2, $eventName2);
        $this->scheduler
            ->expects($this->once())
            ->method('addToSchedule')
            ->with($this->equalTo($corpus1));
        $this->listener->triggerSchedule($event1, $eventName1);
    }
}
