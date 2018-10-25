<?php

namespace Markup\NeedleBundle\DependencyInjection\Compiler;

use Markup\NeedleBundle\Client\BackendClientServiceLocator;
use Markup\NeedleBundle\Terms\NoopTermsService;
use Markup\NeedleBundle\Terms\SolrPrefixTermsService;
use Markup\NeedleBundle\Terms\SolrRegexTermsService;
use Markup\NeedleBundle\Terms\TermsServiceLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class AddTermsPass implements CompilerPassInterface
{
    use AddServiceLocatorArgumentTrait;

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $termsServiceLookup = $container->getParameter('markup_needle.terms_service_lookup');
        $locator = $container->getDefinition(TermsServiceLocator::class);
        foreach ($container->findTaggedServiceIds('markup_needle.terms') as $id => $tags) {
            foreach ($tags as $attributes) {
                if (!isset($attributes['alias'])) {
                    continue;
                }
                $this->registerServiceToLocator($attributes['alias'], $id, $locator);
            }
        }
        foreach ($termsServiceLookup as $corpus => $termsServiceAlias) {
            switch ($termsServiceAlias) {
                case 'solr_regex':
                    $this->registerServiceToLocator(
                        $corpus,
                        $this->registerSolrBasedTermsService(
                            SolrRegexTermsService::class,
                            $corpus,
                            $container
                        ),
                        $locator
                    );
                    break;
                case 'solr_prefix':
                    $this->registerServiceToLocator(
                        $corpus,
                        $this->registerSolrBasedTermsService(
                            SolrPrefixTermsService::class,
                            $corpus,
                            $container
                        ),
                        $locator
                    );
                    break;
                default:
                    $this->registerServiceToLocator($corpus, NoopTermsService::class, $locator);
                    break;
            }
        }
        foreach ($container->findTaggedServiceIds('markup_needle.terms') as $id => $tags) {
            foreach ($tags as $attributes) {
                if (!isset($attributes['alias'])) {
                    continue;
                }
                $this->registerServiceToLocator($attributes['alias'], $id, $locator);
            }
        }
    }

    private function registerSolrBasedTermsService(
        string $termsServiceClass,
        string $corpus,
        ContainerBuilder $container
    ): string {
        $client = (new Definition(\Solarium\Client::class))
            ->setFactory([new Reference(BackendClientServiceLocator::class), 'fetchClientForCorpus'])
            ->setArguments([$corpus])
            ->setAutowired(true)
            ->setPublic(false);
        $clientId = sprintf('markup_needle.terms.client.corpus.%s', $corpus);
        $container->setDefinition($clientId, $client);
        $termsService = (new Definition($termsServiceClass))
            ->setAutowired(true)
            ->setPublic(false)
            ->setArgument('$solarium', new Reference($clientId));
        $termsServiceId = sprintf('markup_needle.terms_service.corpus.%s', $corpus);
        $container->setDefinition($termsServiceId, $termsService);

        return $termsServiceId;
    }
}
