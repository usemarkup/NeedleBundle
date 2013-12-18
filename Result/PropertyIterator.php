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

    /**
     * @param \IteratorAggregate $result
     * @param string             $property
     **/
    public function __construct(\IteratorAggregate $result, $property)
    {
        $this->resultIterator = $result->getIterator();
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
        return $this->getInnerIterator()->next();
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
        return $this->getInnerIterator()->rewind();
    }
}
