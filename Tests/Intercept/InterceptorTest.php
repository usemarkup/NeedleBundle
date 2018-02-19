<?php

namespace Markup\NeedleBundle\Tests\Intercept;

use Markup\NeedleBundle\Intercept\DefinitionInterface;
use Markup\NeedleBundle\Intercept\InterceptInterface;
use Markup\NeedleBundle\Intercept\Interceptor;
use Markup\NeedleBundle\Intercept\MatcherInterface;
use Markup\NeedleBundle\Intercept\TypedInterceptMapperInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
* A test for an interceptor object which can provide intercepts for searches.
*/
class InterceptorTest extends TestCase
{
    protected function setUp()
    {
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->interceptor = new Interceptor($this->eventDispatcher);
    }

    public function testIsInterceptor()
    {
        $this->assertInstanceOf(Interceptor::class, $this->interceptor);
    }

    public function testReturnsNullIfNoDefinitions()
    {
        $this->assertNull($this->interceptor->matchQueryToIntercept('query'));
    }

    public function testInterceptReturnedForMatch()
    {
        $definition = $this->createMock(DefinitionInterface::class);
        $type = 'type';
        $definition
            ->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($type));
        $matcher = $this->createMock(MatcherInterface::class);
        $matcher
            ->expects($this->any())
            ->method('matches')
            ->will($this->returnValue(true));
        $definition
            ->expects($this->any())
            ->method('getMatcher')
            ->will($this->returnValue($matcher));
        $interceptMapper = $this->createMock(TypedInterceptMapperInterface::class);
        $interceptMapper
            ->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($type));
        $intercept = $this->createMock(InterceptInterface::class);
        $interceptMapper
            ->expects($this->any())
            ->method('mapDefinitionToIntercept')
            ->with($this->equalTo($definition))
            ->will($this->returnValue($intercept));
        $this->interceptor->addDefinition($definition);
        $this->interceptor->addInterceptMapper($interceptMapper);
        $this->assertSame($intercept, $this->interceptor->matchQueryToIntercept('query'));
    }

    public function testReturnsNullWithEventDispatchedIfUnresolvedInterceptExceptionThrown()
    {
        $definition = $this->createMock(DefinitionInterface::class);
        $type = 'broken';
        $definition
            ->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($type));
        $matcher = $this->createMock(MatcherInterface::class);
        $matcher
            ->expects($this->any())
            ->method('matches')
            ->will($this->returnValue(true));
        $definition
            ->expects($this->any())
            ->method('getMatcher')
            ->will($this->returnValue($matcher));
        $interceptMapper = $this->createMock(TypedInterceptMapperInterface::class);
        $interceptMapper
            ->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($type));
        $interceptMapper
            ->expects($this->any())
            ->method('mapDefinitionToIntercept')
            ->with($this->equalTo($definition))
            ->will($this->throwException(new \Markup\NeedleBundle\Intercept\UnresolvedInterceptException()));
        $this->interceptor->addDefinition($definition);
        $this->interceptor->addInterceptMapper($interceptMapper);
        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch');
        $this->assertNull($this->interceptor->matchQueryToIntercept('query'));
    }
}
