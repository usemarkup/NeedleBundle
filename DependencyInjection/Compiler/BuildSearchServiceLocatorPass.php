<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\DependencyInjection\Compiler;

use Markup\NeedleBundle\Client\BackendClientServiceLocator;
use Markup\NeedleBundle\Service\ElasticSearchService;
use Markup\NeedleBundle\Service\NoopSearchService;
use Markup\NeedleBundle\Service\SearchServiceLocator;
use Markup\NeedleBundle\Service\SolrSearchService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class BuildSearchServiceLocatorPass implements CompilerPassInterface
{
    use AccessBackendLookupTrait;
    use AddServiceLocatorArgumentTrait;

    public function process(ContainerBuilder $container)
    {
        $backendLookup = $this->getBackendLookup($container);
        $locator = $container->findDefinition(SearchServiceLocator::class);
        foreach ($backendLookup as $corpus => $backend) {
            switch ($backend) {
                case 'solr':
                    $clientId = $this->registerClientClassProvidingId(
                        \Solarium\Client::class,
                        $corpus,
                        $container
                    );
                    $serviceId = $this->registerSearchServiceProvidingId(
                        SolrSearchService::class,
                        $corpus,
                        '$solarium',
                        $clientId,
                        $container
                    );
                    $this->registerServiceToLocator($corpus, $serviceId, $locator);
                    break;
                case 'elasticsearch':
                    $clientId = $this->registerClientClassProvidingId(
                        \Elasticsearch\Client::class,
                        $corpus,
                        $container
                    );
                    $serviceId = $this->registerSearchServiceProvidingId(
                        ElasticSearchService::class,
                        $corpus,
                        '$elastic',
                        $clientId,
                        $container
                    );
                    $this->registerServiceToLocator($corpus, $serviceId, $locator);
                    break;
                default:
                    $this->registerServiceToLocator($corpus, NoopSearchService::class, $locator);
                    break;
            }
        }
    }

    private function registerClientClassProvidingId(string $class, string $corpus, ContainerBuilder $container): string
    {
        $client = (new Definition(\Solarium\Client::class))
            ->setFactory([new Reference(BackendClientServiceLocator::class), 'fetchClientForCorpus'])
            ->setArguments([$corpus])
            ->setAutowired(true)
            ->setPublic(false);
        $clientId = sprintf('markup_needle.service_client.corpus.%s', $corpus);
        $container->setDefinition($clientId, $client);

        return $clientId;
    }

    private function registerSearchServiceProvidingId(
        string $class,
        string $corpus,
        string $clientArg,
        string $clientId,
        ContainerBuilder $container
    ): string {
        $service = (new Definition($class))
            ->setArgument($clientArg, new Reference($clientId))
            ->setAutowired(true)
            ->setPublic(false);
        $serviceId = sprintf('markup_needle.service.corpus.%s', $corpus);
        $container->setDefinition($serviceId, $service);

        return $serviceId;
    }
}
