<?php

namespace Markup\NeedleBundle\DependencyInjection;

use Markup\NeedleBundle\Client\BackendClientServiceLocator;
use Markup\NeedleBundle\Context\ConfiguredContextProvider;
use Markup\NeedleBundle\Corpus\CorpusBackendProvider;
use Markup\NeedleBundle\Intercept\Definition as InterceptDefinition;
use Markup\NeedleBundle\Intercept\NormalizedListMatcher;
use Markup\NeedleBundle\Suggest\SuggestServiceInterface;
use Markup\NeedleBundle\Suggest\SuggestServiceLocator;
use Markup\NeedleBundle\Terms\TermsFieldProviderInterface;
use Markup\NeedleBundle\Terms\TermsServiceInterface;
use Markup\NeedleBundle\Terms\TermsServiceLocator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
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
     * @deprecated (This is constant is transitional until there can be per-corpus client services.)
     */
    const UNITARY_BACKEND_CLIENT = 'markup_needle.solarium.client';

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
        $loader->load('solr.yml');

        $this->loadCorpora($config, $container);
        $this->loadIntercepts($config, $container);
        $this->loadLogSettings($config, $container);
        $this->loadContextServices($config, $container);
        $this->loadSuggestHandler($config, $container);
        $this->loadTerms($config, $container);
        $this->loadTermsField($config, $container);
    }

    /**
     * Loads the backend info.
     *
     * @param array            $config
     * @param ContainerBuilder $container
     **/
    public function loadBackend(array $config, ContainerBuilder $container)
    {
        $knownBackends = ['solr'];
        if (!isset($config['backend']['type'])) {
            return;
        }
        //temporary coercion/ deprecation: "solarium" backend type should now be called "solr"
        $backendType = $config['backend']['type'];
        if ($backendType === 'solarium') {
            @trigger_error(
                'Using "solarium" as a backend is deprecated. The type has been renamed to "solr".',
                E_USER_DEPRECATED
            );
            $backendType = 'solr';
        }
        if (!in_array($backendType, $knownBackends)) {
            throw new InvalidArgumentException('Unknown search backend type.');
        }
        $container->setParameter('markup_needle.backend', $backendType);
        if ($backendType === 'solr') {
            $container->setAlias(self::UNITARY_BACKEND_CLIENT, $config['backend']['client']);
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
        $scheduleEvents = [];
        foreach ($config['corpora'] as $name => $corpusConfig) {
            $scheduleEvents[$name] = $corpusConfig['schedule_index_on_events'];
        }
        $container->setParameter('markup_needle.schedule_events_by_corpus', $scheduleEvents);
        //define client service locator
        $clientLocator = (new Definition(BackendClientServiceLocator::class))
            ->setArguments([
                array_fill_keys(array_keys($config['corpora']), new Reference(self::UNITARY_BACKEND_CLIENT)),
            ])
            ->setPublic(false)
            ->addTag('container.service_locator');
        $container->setDefinition(BackendClientServiceLocator::class, $clientLocator);
        $backendLookup = array_fill_keys(array_keys($config['corpora']), '%markup_needle.backend%');
        $backendProvider = (new Definition(CorpusBackendProvider::class))
            ->setArguments([$backendLookup])
            ->setPublic(false);
        $container->setDefinition(CorpusBackendProvider::class, $backendProvider);
        $termsServiceLookup = array_fill_keys(array_keys($config['corpora']), $config['terms_service']);
        $container->setParameter('markup_needle.terms_service_lookup', $termsServiceLookup);
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
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function loadAllowNullValuesInUpdateFields(array $config, ContainerBuilder $container)
    {
        $container->setParameter('markup_needle.allow_null_values_in_update_fields', $config['allow_null_values_in_update_fields']);
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

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function loadSuggestHandler(array $config, ContainerBuilder $container)
    {
        $container->setAlias('markup_needle.suggest_handler', $config['suggest_handler']);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function loadTerms(array $config, ContainerBuilder $container)
    {
        $container->setParameter('markup_needle.terms.alias', $config['terms_service']);
        $terms = new Definition(
            TermsServiceInterface::class,
            ['%markup_needle.terms.alias%']
        );
        $this->setFactoryOnDefinition($terms, TermsServiceLocator::class, 'get');
        $container->setDefinition('markup_needle.terms', $terms);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function loadTermsField(array $config, ContainerBuilder $container)
    {
        $container->setAlias(TermsFieldProviderInterface::class, $config['terms_field_provider']);
    }

    private function setFactoryOnDefinition(Definition $definition, $factoryService, $factoryMethod)
    {
        $definition->setFactory([new Reference($factoryService), $factoryMethod]);
    }
}
