<?php

namespace Markup\NeedleBundle\Tests\Intercept;

use Markup\NeedleBundle\Intercept\Interceptor;
use Markup\NeedleBundle\Intercept\InterceptorConfigurator;
use Markup\NeedleBundle\Intercept\InterceptorConfiguratorInterface;
use Markup\NeedleBundle\Intercept\TypedInterceptMapperInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class InterceptorConfiguratorTest extends MockeryTestCase
{
    /**
     * @var InterceptorConfigurator
     */
    private $configurator;

    protected function setUp()
    {
        $this->configurator = new InterceptorConfigurator();
    }

    public function testIsConfigurator()
    {
        $this->assertInstanceOf(InterceptorConfiguratorInterface::class, $this->configurator);
    }

    public function testInterceptMappersAddedToInterceptor()
    {
        $mapper1 = m::mock(TypedInterceptMapperInterface::class)->shouldReceive('getType')->andReturn('type1')->getMock();
        $mapper2 = m::mock(TypedInterceptMapperInterface::class)->shouldReceive('getType')->andReturn('type2')->getMock();
        $this->configurator->addInterceptMapper($mapper1);
        $this->configurator->addInterceptMapper($mapper2);
        $interceptor = m::mock(Interceptor::class);
        $interceptor
            ->shouldReceive('addInterceptMapper')
            ->twice();
        $this->configurator->configureInterceptor($interceptor, []);
    }

    public function testAddDefinitionToInterceptor()
    {
        $config = [
            'sale' => [
                'terms' => ['sale'],
                'type' => 'route',
                'route' => 'sale',
            ],
        ];
        $interceptor = m::mock(Interceptor::class);
        $interceptor
            ->shouldReceive('addDefinition')
            ->once();
        $this->configurator->configureInterceptor($interceptor, $config);
    }
}
