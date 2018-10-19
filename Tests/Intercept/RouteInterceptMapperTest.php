<?php

namespace Markup\NeedleBundle\Tests\Intercept;

use Markup\NeedleBundle\Intercept\DefinitionInterface;
use Markup\NeedleBundle\Intercept\RouteInterceptMapper;
use Markup\NeedleBundle\Intercept\TypedInterceptMapperInterface;
use Markup\NeedleBundle\Intercept\UnresolvedInterceptException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
* A test for an intercept mapper for the route definition type.
*/
class RouteInterceptMapperTest extends TestCase
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var RouteInterceptMapper
     */
    private $mapper;

    protected function setUp()
    {
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->mapper = new RouteInterceptMapper($this->urlGenerator);
    }

    public function testIsInterceptMapper()
    {
        $this->assertInstanceOf(TypedInterceptMapperInterface::class, $this->mapper);
    }

    public function testIsRouteType()
    {
        $this->assertEquals('route', $this->mapper->getType());
    }

    public function testMapDefinitionToIntercept()
    {
        $route = 'route';
        $routeParams = ['param' => 'yes'];
        $properties = ['route' => $route, 'params' => $routeParams];
        $definition = $this->createMock(DefinitionInterface::class);
        $definition
            ->expects($this->any())
            ->method('getProperties')
            ->will($this->returnValue($properties));
        $uri = 'i am the uri';
        $this->urlGenerator
            ->expects($this->any())
            ->method('generate')
            ->with($this->equalTo($route), $this->equalTo($routeParams), $this->equalTo(UrlGeneratorInterface::ABSOLUTE_URL))
            ->will($this->returnValue($uri));
        $intercept = $this->mapper->mapDefinitionToIntercept($definition);
        $this->assertInstanceOf('Markup\NeedleBundle\Intercept\InterceptInterface', $intercept);
        $this->assertEquals($uri, $intercept->getUri());
    }

    public function testRoutelessDefinitionThrowsException()
    {
        $this->expectException(UnresolvedInterceptException::class);
        $definition = $this->createMock(DefinitionInterface::class);
        $properties = [];
        $definition
            ->expects($this->any())
            ->method('getProperties')
            ->will($this->returnValue($properties));
        $this->mapper->mapDefinitionToIntercept($definition);
    }

    public function testRouteNotFoundExceptionThrowsUnresolvedException()
    {
        $this->expectException(UnresolvedInterceptException::class);
        $route = 'route';
        $routeParams = ['param' => 'yes'];
        $properties = ['route' => $route, 'params' => $routeParams];
        $definition = $this->createMock(DefinitionInterface::class);
        $definition
            ->expects($this->any())
            ->method('getProperties')
            ->will($this->returnValue($properties));
        $this->urlGenerator
            ->expects($this->any())
            ->method('generate')
            ->will($this->throwException(new \Symfony\Component\Routing\Exception\RouteNotFoundException()));
        $this->mapper->mapDefinitionToIntercept($definition);
    }
}
