<?php

namespace Markup\NeedleBundle\Suggest;

/**
 * An interface for a suggest result.
 */
interface SuggestResultInterface extends \Countable, \Traversable
{
    /**
     * @return ResultGroupInterface[]
     */
    public function getGroups();
}
