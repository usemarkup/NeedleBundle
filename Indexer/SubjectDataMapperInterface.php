<?php

namespace Markup\NeedleBundle\Indexer;

use Markup\NeedleBundle\Exception\IllegalSubjectException;

/**
 * An interface for a mapper object that maps a subject down to a key/value hash containing data to be stored in a backend.
 **/
interface SubjectDataMapperInterface
{
    /**
     * Maps a subject object down to a key/value hash containing data to be stored in a backend.
     *
     * @param  mixed $subject
     * @return array The key/value hash generated.
     * @throws IllegalSubjectException if the subject is illegal and therefore cannot be mapped
     **/
    public function mapSubjectToData($subject);
}
