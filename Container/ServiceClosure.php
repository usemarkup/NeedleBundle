<?php

namespace Markup\NeedleBundle\Container;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ExceptionInterface as DependencyInjectionException;

/**
 * Generic service for generating a prototype from the DIC.
 */
class ServiceClosure
{
    /**
     * An ID for a service to fetch.
     *
     * @var string
     **/
    private $serviceId;

    /**
     * @var ContainerInterface
     **/
    private $container;

    /**
     * @param string             $serviceId
     * @param ContainerInterface $container
     **/
    public function __construct($serviceId, ContainerInterface $container)
    {
        $this->serviceId = $serviceId;
        $this->container = $container;
    }

    public function __invoke()
    {
        try {
            $service = $this->container->get($this->serviceId);
        } catch (DependencyInjectionException $e) {
            return null;
        }

        return $service;
    }
}
