<?php

namespace Markup\NeedleBundle\Suggest;

class EmptySuggestResult implements \IteratorAggregate, SuggestResultInterface
{
    /**
     * @return int
     */
    public function count()
    {
        return 0;
    }

    /**
     * @return string[]
     */
    public function getSuggestions()
    {
        return array();
    }

    public function getIterator()
    {
        return new \ArrayIterator(array());
    }

    /**
     * @return ResultGroupInterface[]
     */
    public function getGroups()
    {
        return array();
    }

    /**
     * @return string[]
     */
    public function getTermSuggestions()
    {
        return array();
    }
}
