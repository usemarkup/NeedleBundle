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
    private $eventCorpora = array();

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
    public function triggerSchedule(Event $event)
    {
        foreach ($this->getCorporaForEvent($event) as $corpus) {
            $this->scheduler->addToSchedule($corpus);
        }
    }

    /**
     * Registers a corpus to schedule an index against an event.
     *
     * @param  string|CorpusInterface $corpus
     * @param  string|Event           $event
     * @return self
     **/
    public function addCorpusForEvent($corpus, $event)
    {
        $corpus = ($corpus instanceof CorpusInterface) ? $corpus->getName() : $corpus;
        $event = ($event instanceof Event) ? $event->getName() : $event;
        if (!isset($this->eventCorpora[$event])) {
            $this->eventCorpora[$event] = array();
        }
        $this->eventCorpora[$event] = array_unique(array_merge($this->eventCorpora[$event], array($corpus)));

        return $this;
    }

    /**
     * @return array
     **/
    private function getCorporaForEvent(Event $event)
    {
        if (!isset($this->eventCorpora[$event->getName()])) {
            return array();
        }

        return $this->eventCorpora[$event->getName()];
    }
}
