<?php

namespace Markup\NeedleBundle\Corpus;

/**
* A provider object for corpora.
*/
class CorpusProvider
{
    /**
     * @var array
     **/
    private $corpora;

    /**
     * Fetches the corpus with the given name. Returns null if none available with that name.
     *
     * @param  string               $name
     * @return CorpusInterface|null
     **/
    public function fetchNamedCorpus($name)
    {
        if (!isset($this->corpora[$name])) {
            return null;
        }

        return $this->corpora[$name];
    }

    /**
     * Adds a corpus to the provider.
     *
     * @param  string          $name
     * @param  CorpusInterface $corpus
     * @return self
     **/
    public function addCorpus($name, CorpusInterface $corpus)
    {
        $this->corpora[$name] = $corpus;

        return $this;
    }
}
