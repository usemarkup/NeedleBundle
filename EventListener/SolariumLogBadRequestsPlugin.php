<?php

namespace Markup\NeedleBundle\EventListener;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Solarium\Core\Event\PostExecuteRequest as SolariumPostExecuteRequestEvent;
use Solarium\Core\Plugin\Plugin as SolariumPlugin;

/**
* A plugin that logs bad request information.
*/
class SolariumLogBadRequestsPlugin extends SolariumPlugin
{
    /**
     * @var LoggerInterface
     **/
    private $logger;

    /**
     * @var bool
     **/
    private $enabled;

    /**
     * @param array $options
     **/
    public function __construct($options = null)
    {
        $this->logger = new NullLogger();
        $this->enabled = true;
        parent::__construct($options);
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    public function setEnabled($whether)
    {
        $this->enabled = $whether;

        return $this;
    }

    protected function initPluginType()
    {
    }

    public function onPostExecute(SolariumPostExecuteRequestEvent $event)
    {
        $response = $event->getResponse();
        if ($response->getStatusCode() != 400) {
            //only interested in 400s
            return;
        }
        $this->logger->error('URI for bad request to Solr: '.$event->getRequest()->getUri());
    }
}
