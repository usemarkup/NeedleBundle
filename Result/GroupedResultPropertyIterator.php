<?php

namespace Markup\NeedleBundle\Result;

use Markup\NeedleBundle\Adapter\GroupedResultAdapter;

/**
* An iterator that can take a grouped search result and emit a certain property of each every document,
 * including those result documents for the 'grouped' documents (non primary)
*/
class GroupedResultPropertyIterator extends \AppendIterator
{
    /**
     * A result iterator.
     *
     * @var \ArrayIterator
     **/
    protected $resultIterator;

    /**
     * The public property to access on each document.
     *
     * @var string
     **/
    protected $property;

    /**
     * @param \IteratorAggregate $result
     * @param string             $property
     **/
    public function __construct(\IteratorAggregate $result, $property)
    {
        parent::__construct();
        $this->resultIterator = new \ArrayIterator();
        $this->property = $property;
        foreach($result->getIterator() as $outerDocument) {
            if (!isset($outerDocument['groups'])) {
                throw new \InvalidArgumentException('Can only iterate documents that have a `groups` property');
            }
            $this->append(new \ArrayIterator($outerDocument['groups']));
        }
    }

    /**
     * @return \Iterator
     */
    public function getInnerIterator()
    {
        return $this->resultIterator;
    }

    public function current()
    {
        $current = parent::current();
        $property = $this->property;

        return $current->$property;
    }

    public function next()
    {
        return parent::next();
    }

    public function key()
    {
        return parent::key();
    }

    public function valid()
    {
        return parent::valid();
    }

    public function rewind()
    {
        return parent::rewind();
    }
}
