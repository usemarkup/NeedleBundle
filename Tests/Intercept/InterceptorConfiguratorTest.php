<?php

namespace Markup\NeedleBundle\Tests\Intercept;

use Markup\NeedleBundle\Intercept\InterceptorConfigurator;
use Mockery as m;

class InterceptorConfiguratorTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->configurator = new InterceptorConfigurator();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testIsConfigurator()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Intercept\InterceptorConfiguratorInterface', $this->configurator);
    }

    public function testInterceptMappersAddedToInterceptor()
    {
        $mapper1 = m::mock('Markup\NeedleBundle\Intercept\TypedInterceptMapperInterface')->shouldReceive('getType')->andReturn('type1')->getMock();
        $mapper2 = m::mock('Markup\NeedleBundle\Intercept\TypedInterceptMapperInterface')->shouldReceive('getType')->andReturn('type2')->getMock();
        $this->configurator->addInterceptMapper($mapper1);
        $this->configurator->addInterceptMapper($mapper2);
        $interceptor = m::mock('Markup\NeedleBundle\Intercept\Interceptor');
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
        $interceptor = m::mock('Markup\NeedleBundle\Intercept\Interceptor');
        $interceptor
            ->shouldReceive('addDefinition')
            ->once();
        $this->configurator->configureInterceptor($interceptor, $config);
    }
}
