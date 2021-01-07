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
        return [];
    }

    public function getIterator()
    {
        return new \ArrayIterator([]);
    }

    /**
     * @return ResultGroupInterface[]
     */
    public function getGroups()
    {
        return [];
    }

    /**
     * @return string[]
     */
    public function getTermSuggestions()
    {
        return [];
    }
}
