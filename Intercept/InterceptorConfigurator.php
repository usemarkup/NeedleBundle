<?php

namespace Markup\NeedleBundle\Intercept;

/**
 * Configurator that can take a config hash for interceptor definitions and configure an interceptor object.
 */
class InterceptorConfigurator implements InterceptorConfiguratorInterface
{
    /**
     * @var TypedInterceptMapperInterface[]
     */
    private $interceptMappers;

    public function __construct()
    {
        $this->interceptMappers = [];
    }

    /**
     * Configures the provided interceptor using the provided definitions config hash.
     *
     * @param Interceptor $interceptor
     * @param array $definitions
     */
    public function configureInterceptor(Interceptor $interceptor, array $definitions)
    {
        foreach ($this->interceptMappers as $mapper) {
            $interceptor->addInterceptMapper($mapper);
        }
        //iterate over definitions
        foreach ($definitions as $definition => $config) {
            if (!isset($config['terms']) || !isset($config['type'])) {
                continue;
            }
            $terms = (is_string($config['terms'])) ? [$config['terms']] : $config['terms'];
            $matcher = new NormalizedListMatcher();
            $matcher->setList($terms);
            $properties = array_diff_key($config, ['name' => true, 'type' => true]);
            $interceptor->addDefinition(
                new Definition(
                    $definition,
                    $matcher,
                    $config['type'],
                    $properties
                )
            );
        }
    }

    /**
     * @param TypedInterceptMapperInterface $mapper
     * @return self
     */
    public function addInterceptMapper(TypedInterceptMapperInterface $mapper)
    {
        $this->interceptMappers[$mapper->getType()] = $mapper;

        return $this;
    }
}
