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
     * @var callable
     */
    private $perSubjectCallback;

    public function __construct(
        iterable $subjects,
        SubjectDocumentGeneratorInterface $documentGenerator,
        ?callable $perSubjectCallback = null
    ) {
        $this->setSubjects($subjects);
        $this->documentGenerator = $documentGenerator;
        $this->perSubjectCallback = $perSubjectCallback ?? function () {
        };
    }

    public function getInnerIterator()
    {
        return $this->subjects;
    }

    /**
     * Sets subjects on this iterator.
     **/
    public function setSubjects(iterable $subjects)
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
     * @return \Solarium\QueryType\Update\Query\Document\DocumentInterface|null
     */
    public function current()
    {
        $subject = $this->getInnerIterator()->current();

        $document = $this
            ->getDocumentGenerator()
            ->createDocumentForSubject($subject);

        ($this->perSubjectCallback)();

        return $document;
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
