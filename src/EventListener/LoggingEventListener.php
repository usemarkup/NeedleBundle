<?php

namespace Markup\NeedleBundle\EventListener;

use Markup\NeedleBundle\Event;
use Psr\Log\LoggerInterface;

/**
* A event listener that can listen to search-related events and log behaviour.
*/
class LoggingEventListener
{
    /**
     * @var LoggerInterface
     **/
    private $logger;

    /**
     * @param LoggerInterface $logger
     **/
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Logs an unresolved intercept, as a WARNING.
     *
     * @param Event\UnresolvedInterceptEvent $event
     **/
    public function logUnresolvedIntercept(Event\UnresolvedInterceptEvent $event)
    {
        $this->logger->warning(
            sprintf(
                'Unresolved intercept for search query "%s" using intercept definition "%s". Exception message: %s',
                $event->getQueryString(),
                $event->getInterceptDefinition()->getName(),
                $event->getExceptionMessage()
            )
        );
    }
}
