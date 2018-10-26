<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\DependencyInjection\Compiler;

use Markup\NeedleBundle\Client\BackendClientServiceLocator;
use Markup\NeedleBundle\Indexer\IndexingMessagerLocator;
use Markup\NeedleBundle\Indexer\NoopIndexingMessager;
use Markup\NeedleBundle\Indexer\SolariumIndexingMessager;
use Markup\NeedleBundle\Indexer\SubjectDataMapperProvider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class BuildIndexingMessagerLocatorPass implements CompilerPassInterface
{
    use AccessBackendLookupTrait;
    use AddServiceLocatorArgumentTrait;

    public function process(ContainerBuilder $container)
    {
        $locator = $container->findDefinition(IndexingMessagerLocator::class);
        $backendLookup = $this->getBackendLookup($container);
        foreach ($backendLookup as $corpus => $backend) {
            if ($backend !== 'solr') {
                $this->registerServiceToLocator($corpus, NoopIndexingMessager::class, $locator);
                continue;
            }
            //following is for solr
            $client = (new Definition(\Solarium\Client::class))
                ->setFactory([new Reference(BackendClientServiceLocator::class), 'fetchClientForCorpus'])
                ->setArguments([$corpus])
                ->setAutowired(true)
                ->setPublic(false);
            $clientId = sprintf('markup_needle.service_client_for_indexer.corpus.%s', $corpus);
            $container->setDefinition($clientId, $client);
            $messager = (new Definition(SolariumIndexingMessager::class))
                ->setArguments([
                    new Reference($clientId),
                    new Reference(SubjectDataMapperProvider::class),
                ])
                ->setPublic(false);
            $messagerId = sprintf('markup_needle.indexing_messager.corpus.%s', $corpus);
            $container->setDefinition($messagerId, $messager);
            $this->registerServiceToLocator($corpus, $messagerId, $locator);
        }
    }
}
