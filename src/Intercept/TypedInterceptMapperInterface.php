<?php

namespace Markup\NeedleBundle\Intercept;

/**
 * An interface for an mapper object that is specific to a search intercept type, that maps a definition to an intercept.
 **/
interface TypedInterceptMapperInterface
{
    /**
     * Gets the type of the interceptor.
     *
     * @return string
     **/
    public function getType();

    /**
     * Maps an intercept definition to an intercept.
     *
     * @param  DefinitionInterface $definition
     * @return InterceptInterface
     **/
    public function mapDefinitionToIntercept(DefinitionInterface $definition);
}
