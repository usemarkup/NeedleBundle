<?php

namespace Markup\NeedleBundle\Result;

/**
* An iterator that can take a search result and emit a certain property of each result document.
*/
class PropertyIterator implements \OuterIterator
{
    /**
     * A result iterator.
     *
     * @var \Iterator
     **/
    private $resultIterator;

    /**
     * The public property to access on each document.
     *
     * @var string
     **/
    private $property;

    public function __construct(\IteratorAggregate $result, string $property)
    {
        $this->resultIterator = new \IteratorIterator($result);
        $this->property = $property;
    }

    public function getInnerIterator()
    {
        return $this->resultIterator;
    }

    public function current()
    {
        $current = $this->getInnerIterator()->current();
        $property = $this->property;

        return $current->$property;
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
}
