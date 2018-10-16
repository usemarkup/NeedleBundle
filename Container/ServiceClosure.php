<?php

namespace Markup\NeedleBundle\Container;

use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ExceptionInterface as DependencyInjectionException;

/**
 * Generic service for generating a prototype from the DIC.
 */
class ServiceClosure
{
    const ALIAS = 'alias';

    /**
     * @var ContainerInterface
     **/
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke()
    {
        try {
            $service = $this->container->get(self::ALIAS);
        } catch (DependencyInjectionException $e) {
            return null;
        }

        return $service;
    }
}
