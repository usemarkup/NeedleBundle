<?php

namespace Markup\NeedleBundle\Intercept;

/**
 * Interface for a configurator object that can configure an interceptor object using a hashed definition config.
 */
interface InterceptorConfiguratorInterface
{
    /**
     * Configures the provided interceptor using the provided definitions config hash.
     *
     * @param Interceptor $interceptor
     * @param array $definitions
     */
    public function configureInterceptor(Interceptor $interceptor, array $definitions);
}
