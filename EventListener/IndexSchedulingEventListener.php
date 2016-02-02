<?php

namespace Markup\NeedleBundle\EventListener;

use Markup\NeedleBundle\Corpus\CorpusInterface;
use Markup\NeedleBundle\Scheduler\IndexScheduler;
use Symfony\Component\EventDispatcher\Event;

/**
* An event listener that allows events to trigger index schedules as long as at least one associated corpus has been registered with the event.
*/
class IndexSchedulingEventListener
{
    /**
     * @var IndexScheduler
     **/
    private $scheduler;

    /**
     * Lists of search corpora grouped/keyed by event name.
     *
     * @var array
     **/
    private $eventCorpora = [];

    /**
     * @param IndexScheduler $scheduler
     **/
    public function __construct(IndexScheduler $scheduler)
    {
        $this->scheduler = $scheduler;
    }

    /**
     * Triggers a schedule based on the event sent and any corpora registered on it.
     *
     * @param Event $event
     **/
    public function triggerSchedule(Event $event, $eventName = null)
    {
        //if using Symfony 2.3, there would be no event name provided by the dispatcher and we'd get it from the event
        $eventName = (null !== $eventName) ? $eventName : $event->getName();
        foreach ($this->getCorporaForEvent($eventName) as $corpus) {
            $this->scheduler->addToSchedule($corpus);
        }
    }

    /**
     * Registers a corpus to schedule an index against an event.
     *
     * @param  string|CorpusInterface $corpus
     * @param  string                 $event  The name of an event.
     * @return self
     **/
    public function addCorpusForEvent($corpus, $event)
    {
        $corpus = ($corpus instanceof CorpusInterface) ? $corpus->getName() : $corpus;
        if (!isset($this->eventCorpora[$event])) {
            $this->eventCorpora[$event] = [];
        }
        $this->eventCorpora[$event] = array_unique(array_merge($this->eventCorpora[$event], [$corpus]));

        return $this;
    }

    /**
     * @var string $event
     * @return array
     **/
    private function getCorporaForEvent($event)
    {
        if (!isset($this->eventCorpora[$event])) {
            return [];
        }

        return $this->eventCorpora[$event];
    }
}
