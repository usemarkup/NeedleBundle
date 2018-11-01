<?php

namespace Markup\NeedleBundle\DependencyInjection;

use Markup\NeedleBundle\Client\BackendClientServiceLocator;
use Markup\NeedleBundle\Context\ConfiguredContextProvider;
use Markup\NeedleBundle\Corpus\CorpusBackendProvider;
use Markup\NeedleBundle\Intercept\Definition as InterceptDefinition;
use Markup\NeedleBundle\Intercept\NormalizedListMatcher;
use Markup\NeedleBundle\Suggest\SuggestHandlerLocator;
use Markup\NeedleBundle\Terms\TermsFieldProviderInterface;
use Markup\NeedleBundle\Terms\TermsFieldProviderLocator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
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

        $loader->load('services.yml');
        $loader->load('solr.yml');
        //just load everything for now
        $loader->load('elasticsearch.yml');

        $this->loadCorpora($config, $container);
        $this->loadIntercepts($config, $container);
        $this->loadLogSettings($config, $container);
        $this->loadContextServices($config, $container);
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
        $scheduleEvents = [];
        foreach ($config['corpora'] as $name => $corpusConfig) {
            $scheduleEvents[$name] = $corpusConfig['schedule_index_on_events'];
        }
        $container->setParameter('markup_needle.schedule_events_by_corpus', $scheduleEvents);
        //define client service locator
        $clientLocator = (new Definition(BackendClientServiceLocator::class))
            ->setArguments([
                array_map(
                    function (array $corpusConfig) {
                        return new Reference($corpusConfig['backend']['client']);
                    },
                    $config['corpora']
                )
            ])
            ->setPublic(false)
            ->addTag('container.service_locator');
        $container->setDefinition(BackendClientServiceLocator::class, $clientLocator);
        $backendLookup = array_map(
            function (array $corpusConfig) {
                return $corpusConfig['backend']['type'];
            },
            $config['corpora']
        );
        $backendProvider = (new Definition(CorpusBackendProvider::class))
            ->setArguments([$backendLookup])
            ->setPublic(false);
        $container->setDefinition(CorpusBackendProvider::class, $backendProvider);
        $termsServiceLookup = array_map(
            function (array $corpusConfig) {
                return $corpusConfig['terms_service'];
            },
            $config['corpora']
        );
        $container->setParameter('markup_needle.terms_service_lookup', $termsServiceLookup);
        $suggestHandlerLocator = (new Definition(SuggestHandlerLocator::class))
            ->setArguments([
                array_map(
                    function (array $corpusConfig) {
                        return new Reference($corpusConfig['suggest_handler']);
                    },
                    $config['corpora']
                )
            ])
            ->setPublic(false)
            ->addTag('container.service_locator');
        $container->setDefinition(SuggestHandlerLocator::class, $suggestHandlerLocator);
        $termsFieldProviderLocator = (new Definition(TermsFieldProviderLocator::class))
            ->setArguments([
                array_map(
                    function (array $corpusConfig) {
                        return new Reference($corpusConfig['terms_field_provider']);
                    },
                    $config['corpora']
                )
            ])
            ->setPublic(false)
            ->addTag('container.service_locator');
        $container->setDefinition(TermsFieldProviderLocator::class, $termsFieldProviderLocator);
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
        $domains = isset($config['intercepts']['domains']) ? $config['intercepts']['domains'] : [];
        $domains = array_unique(array_merge($domains, [$defaultDomain]));
        $interceptors = [];
        foreach ($domains as $domain) {
            $domainedInterceptor = new ChildDefinition('markup_needle.interceptor');
            $container->setDefinition(sprintf('markup_needle.interceptor.%s', $domain), $domainedInterceptor);
            $interceptors[$domain] = $domainedInterceptor;
        }
        if (empty($config['intercepts']['definitions'])) {
            return;
        }
        foreach ($config['intercepts']['definitions'] as $definitionName => $definition) {
            $matcher = new Definition(NormalizedListMatcher::class);
            $matcher->addMethodCall('setList', [$definition['terms']]);
            $matcherName = sprintf('markup_needle.intercept.matcher.%s', $definitionName);
            $matcher->setPublic(false);
            $container->setDefinition($matcherName, $matcher);
            $properties = array_diff_key($definition, ['name' => true, 'type' => true]);
            $interceptDefinition = new Definition(
                InterceptDefinition::class,
                [
                    $definitionName,
                    new Reference($matcherName),
                    $definition['type'],
                    $properties,
                ]
            );
            $domain = (isset($definition['domain'])) ? $definition['domain'] : $defaultDomain;
            $interceptor = $interceptors[$domain];
            $interceptor->addMethodCall('addDefinition', [$interceptDefinition]);
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
     * Loads services and context providers for declared contexts.
     *
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function loadContextServices(array $config, ContainerBuilder $container)
    {
        foreach ($config['context_services'] as $contextName => $contextConfig) {
            $prefix = sprintf('markup_needle.contexts.%s.', $contextName);
            $container->setAlias($prefix.'filter_provider', $contextConfig['filter_provider']);
            $container->setAlias($prefix.'facet_provider', $contextConfig['facet_provider']);
            $container->setAlias($prefix.'facet_set_decorator_provider', $contextConfig['facet_set_decorator_provider']);
            $container->setAlias($prefix.'facet_collator_provider', $contextConfig['facet_collator_provider']);
            $container->setAlias($prefix.'facet_order_provider', $contextConfig['facet_order_provider']);
            $contextProvider = new Definition(
                ConfiguredContextProvider::class,
                [
                    new Reference($prefix.'filter_provider'),
                    new Reference($prefix.'facet_provider'),
                    new Reference($prefix.'facet_set_decorator_provider'),
                    new Reference($prefix.'facet_collator_provider'),
                    new Reference($prefix.'facet_order_provider'),
                    new Reference('markup_needle.configured_interceptor_provider'),
                ]
            );
            $container->setDefinition($prefix.'context_provider', $contextProvider);
        }
    }
}
