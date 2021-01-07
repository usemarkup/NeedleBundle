<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\DependencyInjection\Compiler;

use Markup\NeedleBundle\Client\SolrEndpointAccessor;
use Markup\NeedleBundle\Client\SolrManagedSynonymsClient;
use Markup\NeedleBundle\Synonyms\NoopSynonymClient;
use Markup\NeedleBundle\Synonyms\SolrSynonymClient;
use Markup\NeedleBundle\Synonyms\SynonymClientServiceLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class BuildSynonymClientLocatorPass implements CompilerPassInterface
{
    use AccessBackendLookupTrait;
    use AddServiceLocatorArgumentTrait;

    public function process(ContainerBuilder $container)
    {
        $backendLookup = $this->getBackendLookup($container);
        $locator = (new Definition(SynonymClientServiceLocator::class))
            ->setArguments([[]])
            ->addTag('container.service_locator');
        foreach ($backendLookup as $corpus => $backend) {
            switch ($backend) {
                case 'solr':
                    //first get endpoint accessor
                    $endpointAccessor = $this->makeNewDefinition(
                        SolrEndpointAccessor::class,
                        ['$corpus' => $corpus]
                    );
                    $endpointAccessorId = sprintf('markup_needle.endpoint_accessor.corpus.%s', $corpus);
                    $container->setDefinition($endpointAccessorId, $endpointAccessor);
                    //now create solr managed synonyms client service
                    $managedClient = $this->makeNewDefinition(
                        SolrManagedSynonymsClient::class,
                        ['$endpointAccessor' => new Reference($endpointAccessorId)]
                    );
                    $managedClientId = sprintf('markup_needle.managed_client.corpus.%s', $corpus);
                    $container->setDefinition($managedClientId, $managedClient);
                    //now put it all within solr synonym client service
                    $solrSynonymClient = $this->makeNewDefinition(
                        SolrSynonymClient::class,
                        ['$solrManagedSynonymsClient' => new Reference($managedClientId)]
                    );
                    $solrSynonymClientId = sprintf('markup_needle.solr_synonym_client.corpus.%s', $corpus);
                    $container->setDefinition($solrSynonymClientId, $solrSynonymClient);
                    $this->registerServiceToLocator($corpus, $solrSynonymClientId, $locator);
                    break;
                case 'elasticsearch':
                default:
                    $this->registerServiceToLocator($corpus, NoopSynonymClient::class, $locator);
                    break;
            }
        }
        $container->setDefinition(SynonymClientServiceLocator::class, $locator);
    }

    private function makeNewDefinition(string $class, array $argumentPairs = []): Definition
    {
        $definition = (new Definition($class))
            ->setAutowired(true)
            ->setPublic(false);
        foreach ($argumentPairs as $argument => $argValue) {
            $definition->setArgument($argument, $argValue);
        }

        return $definition;
    }
}
