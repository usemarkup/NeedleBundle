<?php

namespace Markup\NeedleBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MarkupNeedleExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->loadDebug($config, $container);
        $this->loadBackend($config, $container);
        $this->loadAllowNullValuesInUpdateFields($config, $container);

        $loader->load('services.yml');

        $this->loadCorpora($config, $container);
        $this->loadIntercepts($config, $container);
        $this->loadLogSettings($config, $container);
    }

    /**
     * Loads the backend info.
     *
     * @param array            $config
     * @param ContainerBuilder $container
     **/
    public function loadBackend(array $config, ContainerBuilder $container)
    {
        $knownBackends = array('solarium');
        if (!isset($config['backend']['type'])) {
            return;
        }
        if (!in_array($config['backend']['type'], $knownBackends)) {
            throw new InvalidArgumentException('Unknown search backend type.');
        }
        $container->setParameter('markup_needle.backend', $config['backend']['type']);
        if ($config['backend']['type'] === 'solarium') {
            $container->setAlias('markup_needle.solarium.client', $config['backend']['client']);
        }
    }

    /**
     * Loads the debug flag.
     *
     * @param array            $config
     * @param ContainerBuilder $container
     **/
    private function loadDebug(array $config, ContainerBuilder $container)
    {
        $container->setParameter('markup_needle.debug', $config['debug']);
    }

    /**
     * Loads information about the corpora.
     *
     * @param array            $config
     * @param ContainerBuilder $container
     **/
    private function loadCorpora(array $config, ContainerBuilder $container)
    {
        $scheduleEvents = array();
        foreach ($config['corpora'] as $name => $corpusConfig) {
            $scheduleEvents[$name] = $corpusConfig['schedule_index_on_events'];
        }
        $container->setParameter('markup_needle.schedule_events_by_corpus', $scheduleEvents);
    }

    /**
     * Loads the definitions for search intercepts.
     *
     * @param array            $config
     * @param ContainerBuilder $container
     **/
    private function loadIntercepts(array $config, ContainerBuilder $container)
    {
        $defaultDomain = $config['intercepts']['default_domain'];
        $domains = isset($config['intercepts']['domains']) ? $config['intercepts']['domains'] : array();
        $domains = array_unique(array_merge($domains, array($defaultDomain)));
        $interceptors = array();
        foreach ($domains as $domain) {
            $domainedInterceptor = new DefinitionDecorator('markup_needle.interceptor');
            $container->setDefinition(sprintf('markup_needle.interceptor.%s', $domain), $domainedInterceptor);
            $interceptors[$domain] = $domainedInterceptor;
        }
        if (empty($config['intercepts']['definitions'])) {
            return;
        }
        foreach ($config['intercepts']['definitions'] as $definitionName => $definition) {
            $matcher = new Definition('%markup_needle.intercept.matcher.normalized_list.class%');
            $matcher->addMethodCall('setList', array($definition['terms']));
            $matcherName = sprintf('markup_needle.intercept.matcher.%s', $definitionName);
            $matcher->setPublic(false);
            $container->setDefinition($matcherName, $matcher);
            $properties = array_diff_key($definition, array('name' => true, 'type' => true));
            $interceptDefinition = new Definition(
                '%markup_needle.intercept.definition.class%',
                array(
                    $definitionName,
                    new Reference($matcherName),
                    $definition['type'],
                    $properties,
                )
            );
            $domain = (isset($definition['domain'])) ? $definition['domain'] : $defaultDomain;
            $interceptor = $interceptors[$domain];
            $interceptor->addMethodCall('addDefinition', array($interceptDefinition));
        }
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     **/
    private function loadLogSettings(array $config, ContainerBuilder $container)
    {
        $container->setParameter('markup_needle.solr.log_bad_requests_plugin.enabled', $config['log_bad_requests']);
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function loadAllowNullValuesInUpdateFields(array $config, ContainerBuilder $container)
    {
        $container->setParameter('markup_needle.allow_null_values_in_update_fields', $config['allow_null_values_in_update_fields']);
    }
}
