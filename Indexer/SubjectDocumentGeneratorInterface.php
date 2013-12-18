<?php

namespace Markup\NeedleBundle\Indexer;

use Solarium\QueryType\Update\Query\Document\Document;

/**
 * An interface for a document generator from a subject.
 **/
interface SubjectDocumentGeneratorInterface
{
    /**
     * Creates a Solarium document for a subject, if this can be achieved.
     *
     * @param array|object $subject
     * @return Document|null
     **/
    public function createDocumentForSubject($subject);
}
