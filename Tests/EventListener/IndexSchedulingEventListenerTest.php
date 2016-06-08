<?php

namespace Markup\NeedleBundle\Tests\EventListener;

use Markup\NeedleBundle\EventListener\IndexSchedulingEventListener;
use Markup\NeedleBundle\Scheduler\IndexScheduler;
use Mockery as m;
use Symfony\Component\EventDispatcher\Event;

/**
* A test for an event listener that schedules a search index.
*/
class IndexSchedulingEventListenerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->scheduler = m::mock(IndexScheduler::class);
        $this->listener = new IndexSchedulingEventListener($this->scheduler);
    }

    public function testAddScheduledIndexWithAddedCorpora()
    {
        $corpus1 = 'corpus1';
        $corpus2 = 'corpus2';
        $eventName1 = 'event1';
        $eventName2 = 'event2';
        $event1 = m::mock(Event::class);
        $event1
            ->shouldReceive('getName')
            ->andReturn($eventName1);
        $this->listener->addCorpusForEvent($corpus1, $eventName1);
        $this->listener->addCorpusForEvent($corpus2, $eventName2);
        $this->scheduler
            ->shouldReceive('addToSchedule')
            ->with($corpus1)
            ->once();
        $this->listener->triggerSchedule($event1);
    }
}
