<?php

namespace Markup\NeedleBundle\Tests\Intercept;

use Markup\NeedleBundle\Intercept\ConfiguredInterceptorProvider;
use Markup\NeedleBundle\Intercept\Interceptor;
use Markup\NeedleBundle\Intercept\InterceptorConfiguratorInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class ConfiguredInterceptorProviderTest extends MockeryTestCase
{
    /**
     * @var Interceptor|m\MockInterface
     */
    private $interceptor;

    /**
     * @var InterceptorConfiguratorInterface|m\MockInterface
     */
    private $configurator;

    /**
     * @var ConfiguredInterceptorProvider
     */
    private $provider;

    protected function setUp()
    {
        $this->interceptor = m::mock(Interceptor::class);
        $this->configurator = m::mock(InterceptorConfiguratorInterface::class);
        $this->provider = new ConfiguredInterceptorProvider($this->interceptor, $this->configurator);
    }

    public function testCreateInterceptor()
    {
        $config = [
            'sale' => [
                'terms' => ['sale'],
                'type' => 'route',
                'route' => 'sale',
            ],
        ];
        $this->configurator
            ->shouldReceive('configureInterceptor')
            ->with($this->interceptor, $config)
            ->once();
        $this->assertSame($this->interceptor, $this->provider->createInterceptor($config));
    }
}
