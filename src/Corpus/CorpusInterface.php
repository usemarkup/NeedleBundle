<?php

namespace Markup\NeedleBundle\Corpus;

/**
 * An interface for a search corpus, i.e. a body of subjects that map to documents to be searched on.
 **/
interface CorpusInterface
{
    /**
     * Gets an iteration over the subjects of this corpus. This is not the same as the documents that are sent to a search backend, which are optimised for searching on.  These are the original objects in the application domain.
     *
     * @return \Traversable
     **/
    public function getSubjectIteration();

    /**
     * Gets the name of the corpus.
     *
     * @return string
     **/
    public function getName();
}
