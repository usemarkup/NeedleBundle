<?php

namespace Markup\NeedleBundle\Indexer;

/**
* An iterator that can take an iteration of subjects and emit Solarium documents.
*/
class SubjectDocumentIterator implements \OuterIterator
{
    /**
     * An iteration of subjects.
     *
     * @var \Iterator
     **/
    private $subjects;

    /**
     * A generator object that can take an individual subject and return a Solarium document, if one is achievable.
     *
     * @var SubjectDocumentGeneratorInterface
     **/
    private $documentGenerator;

    /**
     * @var callable[]
     */
    private $callbacks;

    /**
     * A list of callbacks that should be called with each subject on each iteration.
     */

    /**
     * @param array|\Iterator                   $subjects
     * @param SubjectDocumentGeneratorInterface $documentGenerator
     * @param callable                          $callbacks
     **/
    public function __construct($subjects, SubjectDocumentGeneratorInterface $documentGenerator, array $callbacks = [])
    {
        $this->setSubjects($subjects);
        $this->documentGenerator = $documentGenerator;
        $this->callbacks = $callbacks;
    }

    public function getInnerIterator()
    {
        return $this->subjects;
    }

    /**
     * Sets subjects on this iterator.
     *
     * @param array|\Traversable $subjects
     **/
    public function setSubjects($subjects)
    {
        if (is_array($subjects)) {
            $this->subjects = new \ArrayIterator($subjects);
        } elseif ($subjects instanceof \Iterator) {
            $this->subjects = $subjects;
        } elseif ($subjects instanceof \Traversable) {
            $this->subjects = new \IteratorIterator($subjects);
        } else {
            throw new \InvalidArgumentException(sprintf('%s needs an array or a traversable object containing subjects for document generation.', __METHOD__));
        }
    }

    /**
     * Gets an iteration of the subjects in this iterator.
     *
     * @return \Iterator
     **/
    public function getSubjects()
    {
        return $this->getInnerIterator();
    }

    /**
     * Returns a Solarium document, if one is achievable (otherwise returns null).
     *
     * @return \Solarium\QueryType\Update\Query\Document\Document|null
     */
    public function current()
    {
        $subject = $this->getInnerIterator()->current();
        foreach ($this->callbacks as $callback) {
            call_user_func($callback, $subject);
        }

        return $this
            ->getDocumentGenerator()
            ->createDocumentForSubject($subject);
    }

    public function next()
    {
        $this->getInnerIterator()->next();
    }

    public function key()
    {
        return $this->getInnerIterator()->key();
    }

    public function valid()
    {
        return $this->getInnerIterator()->valid();
    }

    public function rewind()
    {
        $this->getInnerIterator()->rewind();
    }

    /**
     * @return SubjectDocumentGeneratorInterface
     **/
    private function getDocumentGenerator()
    {
        return $this->documentGenerator;
    }
}
