<?php

namespace Markup\NeedleBundle\Intercept;

use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
* An intercept mapper for the route definition type.
*/
class RouteInterceptMapper implements TypedInterceptMapperInterface
{
    /**
     * @var UrlGeneratorInterface
     **/
    private $urlGenerator;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     **/
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     **/
    public function getType()
    {
        return 'route';
    }

    /**
     * {@inheritdoc}
     **/
    public function mapDefinitionToIntercept(DefinitionInterface $definition)
    {
        $properties = $definition->getProperties();
        //must have route property defined
        if (!isset($properties['route'])) {
            throw new UnresolvedInterceptException('Route type definition did not contain a "route" property.');
        }
        $route = $properties['route'];
        $routeParams = (isset($properties['params'])) ? $properties['params'] : array();
        try {
            $uri = $this->urlGenerator->generate($route, $routeParams, true);
        } catch (RouteNotFoundException $e) {
            throw new UnresolvedInterceptException(sprintf('Route "%s" could not be resolved.', $route));
        }

        return new Intercept($uri);
    }
}
