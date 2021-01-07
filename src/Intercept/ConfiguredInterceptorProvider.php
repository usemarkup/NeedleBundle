<?php

namespace Markup\NeedleBundle\Intercept;

class ConfiguredInterceptorProvider
{
    /**
     * @var Interceptor
     */
    private $interceptor;

    /**
     * @var InterceptorConfiguratorInterface
     */
    private $interceptorConfigurator;

    public function __construct(Interceptor $interceptor, InterceptorConfiguratorInterface $interceptorConfigurator)
    {
        $this->interceptor = $interceptor;
        $this->interceptorConfigurator = $interceptorConfigurator;
    }

    /**
     * @param array $config
     * @return InterceptorInterface
     */
    public function createInterceptor(array $config)
    {
        $this->interceptorConfigurator->configureInterceptor($this->interceptor, $config);

        return $this->interceptor;
    }
}
