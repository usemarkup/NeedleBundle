<?php

namespace Markup\NeedleBundle\Indexer;

use Markup\NeedleBundle\Corpus\CorpusInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * An object that can provide callbacks (through services) for a given corpus.
 */
class IndexCallbackProvider
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array A list of service ID lists, keyed by corpus name.
     */
    private $corpusServices;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->corpusServices = array();
    }

    /**
     * @param CorpusInterface|string $corpus
     * @return callable[]
     */
    public function getCallbacksForCorpus($corpus)
    {
        if (!isset($this->corpusServices[$this->getNameForCorpus($corpus)])) {
            return array();
        }

        $container = $this->container;

        return array_map(function ($serviceId) use ($container) {
            return $container->get($serviceId);
        }, $this->corpusServices[$this->getNameForCorpus($corpus)]);
    }

    /**
     * @param CorpusInterface|string $corpus
     * @param array                  $serviceIds
     * @return self
     */
    public function setCallbacksForCorpus($corpus, array $serviceIds)
    {
        $this->corpusServices[$this->getNameForCorpus($corpus)] = $serviceIds;

        return $this;
    }

    /**
     * @param CorpusInterface|string $corpus
     * @return string
     */
    private function getNameForCorpus($corpus)
    {
        return ($corpus instanceof CorpusInterface) ? $corpus->getName() : $corpus;
    }
}
