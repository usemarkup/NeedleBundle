<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Corpus;

class CorpusBackendProvider
{
    /**
     * @var string[]
     */
    private $backendsKeyedByCorpus;

    public function __construct(array $backendsKeyedByCorpus)
    {
        $this->backendsKeyedByCorpus = $backendsKeyedByCorpus;
    }

    public function getBackendForCorpus(string $corpus): string
    {
        $backend = $this->backendsKeyedByCorpus[$corpus] ?? null;
        if (null === $backend) {
            throw new \OutOfRangeException(sprintf('Was given unexpected corpus name "%s".', $corpus));
        }

        return $backend;
    }
}
