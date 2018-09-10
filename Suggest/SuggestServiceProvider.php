<?php

namespace Markup\NeedleBundle\Suggest;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A provider of suggest services, using the Symfony DI container.
 */
class SuggestServiceProvider
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array A keyed array of service IDs.
     */
    private $services;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->services = [];
    }

    /**
     * @param string $alias
     * @return SuggestServiceInterface
     * @throws \InvalidArgumentException
     */
    public function getServiceForAlias($alias)
    {
        if (!isset($this->services[$alias])) {
            throw new \InvalidArgumentException(sprintf('There is no registered suggester service with the alias "%s".', $alias));
        }
        if (!$this->container->has($this->services[$alias])) {
            throw new \InvalidArgumentException(sprintf('The service "%s" referred to by the alias "%s" is not registered with the Symfony DI container.', $this->services[$alias], $alias));
        }

        /** @var SuggestServiceInterface $suggestService */
        $suggestService = $this->container->get($this->services[$alias]);

        return $suggestService;
    }

    /**
     * @param string $serviceId
     * @param string $alias
     * @return self
     */
    public function addService($serviceId, $alias)
    {
        $this->services[$alias] = $serviceId;

        return $this;
    }
}
