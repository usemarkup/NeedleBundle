<?php

namespace Markup\NeedleBundle\Intercept;

class ConfiguredInterceptorProvider
{
    /**
     * @var callable
     */
    private $interceptorClosure;

    /**
     * @var InterceptorConfiguratorInterface
     */
    private $interceptorConfigurator;

    /**
     * @param callable $interceptorClosure
     * @param InterceptorConfiguratorInterface $interceptorConfigurator
     */
    public function __construct($interceptorClosure, InterceptorConfiguratorInterface $interceptorConfigurator)
    {
        if (!is_callable($interceptorClosure)) {
            throw new \InvalidArgumentException('Interceptor closure must be callable.');
        }
        $this->interceptorClosure = $interceptorClosure;
        $this->interceptorConfigurator = $interceptorConfigurator;
    }

    /**
     * @param array $config
     * @return InterceptorInterface
     */
    public function createInterceptor(array $config)
    {
        $interceptor = call_user_func($this->interceptorClosure);
        if (!$interceptor instanceof Interceptor) {
            throw new \RuntimeException('Interceptor closure did not create an interceptor.');
        }
        $this->interceptorConfigurator->configureInterceptor($interceptor, $config);

        return $interceptor;
    }
} 
