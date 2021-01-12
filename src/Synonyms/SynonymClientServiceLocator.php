<?php

namespace Markup\NeedleBundle\Synonyms;

use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * A locator that, given a corpus, can provide the right synonym client.
 */
class SynonymClientServiceLocator extends ServiceLocator
{
    public function getSynonymClientForCorpus(string $corpus): SynonymClientInterface
    {
        return $this->get($corpus);
    }
}
