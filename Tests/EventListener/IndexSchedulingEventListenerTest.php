<?php

namespace Markup\NeedleBundle\Tests\EventListener;

use Markup\NeedleBundle\EventListener\IndexSchedulingEventListener;
use Markup\NeedleBundle\Scheduler\IndexScheduler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\Event;

/**
* A test for an event listener that schedules a search index.
*/
class IndexSchedulingEventListenerTest extends TestCase
{
    public function setUp()
    {
        $this->scheduler = $this->createMock(IndexScheduler::class);
        $this->listener = new IndexSchedulingEventListener($this->scheduler);
    }

    public function testAddScheduledIndexWithAddedCorpora()
    {
        $corpus1 = 'corpus1';
        $corpus2 = 'corpus2';
        $eventName1 = 'event1';
        $eventName2 = 'event2';
        $event1 = $this->createMock(Event::class);
        $this->listener->addCorpusForEvent($corpus1, $eventName1);
        $this->listener->addCorpusForEvent($corpus2, $eventName2);
        $this->scheduler
            ->expects($this->once())
            ->method('addToSchedule')
            ->with($this->equalTo($corpus1));
        $this->listener->triggerSchedule($event1, $eventName1);
    }
}
