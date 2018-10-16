<?php

namespace Markup\NeedleBundle\Indexer;

use Markup\NeedleBundle\Corpus\CorpusInterface;
use Markup\NeedleBundle\DependencyInjection\ServiceCollection;

/**
 * An object that can provide callbacks (through services) for a given corpus.
 */
class IndexCallbackProvider
{
    /**
     * @var ServiceCollection[]
     */
    private $corpusServiceCollections;

    public function __construct(array $corpusServiceCollections)
    {
        $this->corpusServiceCollections = $corpusServiceCollections;
    }

    /**
     * @param CorpusInterface|string $corpus
     * @return iterable
     */
    public function getCallbacksForCorpus($corpus)
    {
        $corpusName = $this->getNameForCorpus($corpus);
        if (!isset($this->corpusServiceCollections[$corpusName])) {
            return [];
        }

        return $this->corpusServiceCollections[$corpusName];
    }

    /**
     * @param CorpusInterface|string $corpus
     * @return string
     */
    private function getNameForCorpus($corpus): string
    {
        return ($corpus instanceof CorpusInterface) ? $corpus->getName() : $corpus;
    }
}
