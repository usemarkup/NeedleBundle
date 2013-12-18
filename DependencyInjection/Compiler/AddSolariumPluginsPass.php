<?php

namespace Markup\NeedleBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
* Adds plugins to the Solarium client if there is one.
*/
class AddSolariumPluginsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $solariumClientId = 'markup_needle.solarium.client';
        if (!$container->has('markup_needle.solarium.client')) {
            return;
        }

        $knownPluginIds = [
            'log_bad_requests' => 'markup_needle.solarium.plugin.log_bad_requests',
        ];
        $solarium = $container->findDefinition($solariumClientId);
        foreach ($knownPluginIds as $key => $pluginId) {
            $solarium->addMethodCall('registerPlugin', array($key, new Reference($pluginId)));
        }
    }
}
