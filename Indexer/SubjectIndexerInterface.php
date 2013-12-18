<?php

namespace Markup\NeedleBundle\Indexer;

/**
 * An interface for a subject indexer object (which may run indexing directly or defer it somehow).
 **/
interface SubjectIndexerInterface
{
    /**
     * Adds an update instruction for the provided subject, to be executed when this indexer is run.
     *
     * @param  mixed                                                           $subject
     * @return self
     * @throws \Markup\NeedleBundle\Exception\InvalidSubjectTypeException if subject is of invalid type
     **/
    public function addUpdateForSubject($subject);

    /**
     * Adds a delete instruction for the provided subject, to be executed when this indexer is run.
     *
     * @param  mixed                                                           $subject
     * @return self
     * @throws \Markup\NeedleBundle\Exception\InvalidSubjectTypeException if subject is of invalid type
     **/
    public function addDeleteForSubject($subject);

    /**
     * Executes the indexing (and clears the indexer of pending instructions).
     **/
    public function runIndex();
}
