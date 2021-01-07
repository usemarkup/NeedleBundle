<?php

namespace Markup\NeedleBundle\DependencyInjection\Compiler;

use Markup\NeedleBundle\Corpus\CorpusProvider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
* Registers tagged corpora.
*/
class AddCorporaPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $corpusProviderId = CorpusProvider::class;
        if (!$container->has($corpusProviderId)) {
            return;
        }

        $corpusProvider = $container->findDefinition($corpusProviderId);
        foreach ($container->findTaggedServiceIds('markup_needle.corpus') as $id => $tags) {
            foreach ($tags as $attributes) {
                if (empty($attributes['alias'])) {
                    continue;
                }
                $corpusProvider->addMethodCall('addCorpus', [$attributes['alias'], new Reference($id)]);
            }
        }
    }
}
