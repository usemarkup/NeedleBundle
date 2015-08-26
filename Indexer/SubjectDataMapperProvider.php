<?php

namespace Markup\NeedleBundle\Indexer;

/**
* A provider of subject to document data mappers.
*/
class SubjectDataMapperProvider
{
    /**
     * A collection of mappers keyed by corpus.
     *
     * @var array
     **/
    private $mappers = [];

    /**
     * Fetches the mapper that is associated with the given corpus.
     *
     * @param  string                     $corpus
     * @return SubjectDataMapperInterface
     * @throws InvalidArgumentException   if corpus has no registered mapper associated
     **/
    public function fetchMapperForCorpus($corpus)
    {
        if (!isset($this->mappers[$corpus])) {
            throw new \InvalidArgumentException(sprintf('The corpus "%s" did not have a mapper associated with it. Known mappers are: %s.', $corpus, implode(', ', array_keys($this->mappers))));
        }

        return $this->mappers[$corpus];
    }

    /**
     * Adds a mapper to the provider.
     *
     * @param  string                     $corpus
     * @param  SubjectDataMapperInterface $subjectDataMapper
     * @return self
     **/
    public function addMapper($corpus, SubjectDataMapperInterface $subjectDataMapper)
    {
        $this->mappers[$corpus] = $subjectDataMapper;

        return $this;
    }
}
