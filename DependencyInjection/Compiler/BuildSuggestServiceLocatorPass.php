<?php

namespace Markup\NeedleBundle\DependencyInjection\Compiler;

use Markup\NeedleBundle\Client\BackendClientServiceLocator;
use Markup\NeedleBundle\Suggest\NoopSuggestService;
use Markup\NeedleBundle\Suggest\SolrSuggestService;
use Markup\NeedleBundle\Suggest\SuggestServiceLocator;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class BuildSuggestServiceLocatorPass implements CompilerPassInterface
{
    use AccessBackendLookupTrait;
    use AddServiceLocatorArgumentTrait;

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $backendLookup = $this->getBackendLookup($container);
        $locator = $container->findDefinition(SuggestServiceLocator::class);
        foreach ($backendLookup as $corpus => $backend) {
            switch ($backend) {
                case 'solr':
                    $client = (new Definition(\Solarium\Client::class))
                        ->setFactory([new Reference(BackendClientServiceLocator::class), 'fetchClientForCorpus'])
                        ->setArguments([$corpus])
                        ->setAutowired(true)
                        ->setPublic(false);
                    $clientId = sprintf('markup_needle.service_client_for_suggester.corpus.%s', $corpus);
                    $container->setDefinition($clientId, $client);
                    $suggester = (new Definition(SolrSuggestService::class))
                        ->setArguments([
                            new Reference($clientId),
                            new Reference(LoggerInterface::class),
                            new Reference('markup_needle.suggest_handler'),
                        ])
                        ->setPublic(false);
                    $suggesterId = sprintf('markup_needle.suggester.corpus.%s', $corpus);
                    $container->setDefinition($suggesterId, $suggester);
                    $this->registerServiceToLocator($corpus, $suggesterId, $locator);
                    break;
                case 'elasticsearch':
                default:
                    $this->registerServiceToLocator($corpus, NoopSuggestService::class, $locator);
                    break;
            }
        }
    }
}
