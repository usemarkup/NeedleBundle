<?php

namespace Markup\NeedleBundle\Tests\Intercept;

use Markup\NeedleBundle\Intercept\ConfiguredInterceptorProvider;
use Mockery as m;

class ConfiguredInterceptorProviderTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->interceptor = m::mock('Markup\NeedleBundle\Intercept\Interceptor');
        $that = $this;
        $closure = function () use ($that) {
            return $that->interceptor;
        };
        $this->configurator = m::mock('Markup\NeedleBundle\Intercept\InterceptorConfiguratorInterface');
        $this->provider = new ConfiguredInterceptorProvider($closure, $this->configurator);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testCreateInterceptor()
    {
        $config = array(
            'sale' => array(
                'terms' => array('sale'),
                'type' => 'route',
                'route' => 'sale',
            ),
        );
        $this->configurator
            ->shouldReceive('configureInterceptor')
            ->with($this->interceptor, $config)
            ->once();
        $this->assertSame($this->interceptor, $this->provider->createInterceptor($config));
    }
}
