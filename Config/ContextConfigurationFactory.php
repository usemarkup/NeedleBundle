<?php

namespace Markup\NeedleBundle\Config;

/**
 * Simple factory class to create a config object from a hash.
 */
class ContextConfigurationFactory
{
    /**
     * @param array $configHash
     * @return ContextConfigurationInterface
     */
    public function createConfigFromHash(array $configHash)
    {
        return new ContextConfiguration($configHash);
    }
}
