<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\DependencyInjection\Compiler;

use Markup\NeedleBundle\Client\BackendClientServiceLocator;
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
        $locator = $container->getDefinition(SearchServiceLocator::class);
        foreach ($backendLookup as $corpus => $backend) {
            //hard-code solr a little for now - this can be added to and then generalised
            if ($backend !== 'solr') {
                $this->registerServiceToLocator($corpus, NoopSearchService::class, $locator);
                continue;
            }
            //following is for solr
            $client = (new Definition(\Solarium\Client::class))
                ->setFactory([new Reference(BackendClientServiceLocator::class), 'fetchClientForCorpus'])
                ->setArguments([$corpus])
                ->setAutowired(true)
                ->setPublic(false);
            $clientId = sprintf('markup_needle.service_client.corpus.%s', $corpus);
            $container->setDefinition($clientId, $client);
            $service = (new Definition(SolrSearchService::class))
                ->setArgument('$solarium', new Reference($clientId))
                ->setAutowired(true)
                ->setPublic(false);
            $serviceId = sprintf('markup_needle.service.corpus.%s', $corpus);
            $container->setDefinition($serviceId, $service);
            $this->registerServiceToLocator($corpus, $serviceId, $locator);
        }
    }
}
