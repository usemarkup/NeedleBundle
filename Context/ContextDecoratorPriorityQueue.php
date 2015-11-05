<?php

namespace Markup\NeedleBundle\Context;

use SplPriorityQueue;

/**
 * Class ContextDecoratorPriorityQueue
 *
 * @package Markup\NeedleBundle\Context
 */
class ContextDecoratorPriorityQueue extends \SplPriorityQueue
{
    protected $queueOrder = PHP_INT_MAX;

    /**
     * @param mixed $datum
     * @param mixed $priority
     */
    public function insert($datum, $priority)
    {
        if (is_int($priority)) {
            $priority = [$priority, $this->queueOrder--];
        }
        parent::insert($datum, $priority);
    }
}
