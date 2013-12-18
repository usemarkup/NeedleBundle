<?php

namespace Markup\NeedleBundle\Tests\Intercept;

use Markup\NeedleBundle\Intercept\Interceptor;

/**
* A test for an interceptor object which can provide intercepts for searches.
*/
class InterceptorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->eventDispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->interceptor = new Interceptor($this->eventDispatcher);
    }

    public function testIsInterceptor()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Intercept\Interceptor', $this->interceptor);
    }

    public function testReturnsNullIfNoDefinitions()
    {
        $this->assertNull($this->interceptor->matchQueryToIntercept('query'));
    }

    public function testInterceptReturnedForMatch()
    {
        $definition = $this->getMock('Markup\NeedleBundle\Intercept\DefinitionInterface');
        $type = 'type';
        $definition
            ->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($type));
        $matcher = $this->getMock('Markup\NeedleBundle\Intercept\MatcherInterface');
        $matcher
            ->expects($this->any())
            ->method('matches')
            ->will($this->returnValue(true));
        $definition
            ->expects($this->any())
            ->method('getMatcher')
            ->will($this->returnValue($matcher));
        $interceptMapper = $this->getMock('Markup\NeedleBundle\Intercept\TypedInterceptMapperInterface');
        $interceptMapper
            ->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($type));
        $intercept = $this->getMock('Markup\NeedleBundle\Intercept\InterceptInterface');
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
        $definition = $this->getMock('Markup\NeedleBundle\Intercept\DefinitionInterface');
        $type = 'broken';
        $definition
            ->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($type));
        $matcher = $this->getMock('Markup\NeedleBundle\Intercept\MatcherInterface');
        $matcher
            ->expects($this->any())
            ->method('matches')
            ->will($this->returnValue(true));
        $definition
            ->expects($this->any())
            ->method('getMatcher')
            ->will($this->returnValue($matcher));
        $interceptMapper = $this->getMock('Markup\NeedleBundle\Intercept\TypedInterceptMapperInterface');
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
