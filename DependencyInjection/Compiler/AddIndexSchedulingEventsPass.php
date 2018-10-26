<?php

namespace Markup\NeedleBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
* Registers events on which scheduled indexes should happen on different corpora.
*/
class AddIndexSchedulingEventsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $scheduledEventsParameterId = 'markup_needle.schedule_events_by_corpus';
        $schedulingListenerId = 'markup_needle.listener.index_scheduling';
        if (!$container->hasParameter($scheduledEventsParameterId) || !$container->has($schedulingListenerId)) {
            return;
        }

        $schedulingListener = $container->findDefinition($schedulingListenerId);
        $scheduledEvents = $container->getParameter($scheduledEventsParameterId);
        foreach ($scheduledEvents as $corpus => $events) {
            foreach ($events as $event) {
                $schedulingListener->addTag('kernel.event_listener', ['event' => $event, 'method' => 'triggerSchedule']);
                //add explicit record of corpus so listener can know which corpora to schedule against on which events
                $schedulingListener->addMethodCall('addCorpusForEvent', [$corpus, $event]);
            }
        }
    }
}
