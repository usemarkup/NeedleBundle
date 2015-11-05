<?php

namespace Markup\NeedleBundle\Tests\Context;

use Markup\NeedleBundle\Context\ContextDecoratorPriorityQueue;

class ContextDecoratorPriorityQueueTest extends \PHPUnit_Framework_TestCase
{
    public function testAddingWithoutOrder()
    {
        $queue = new ContextDecoratorPriorityQueue();
        $queue->insert('first', 100);
        $queue->insert('second', 100);
        $queue->insert('third', 100);
        $queue->insert('fourth', 100);

        $this->assertEquals('first|second|third|fourth', $this->implodeQueue($queue));
    }

    public function testOrder()
    {
        $queue = new ContextDecoratorPriorityQueue();
        $queue->insert('fourth', -44);
        $queue->insert('second', 100);
        $queue->insert('first', 150);
        $queue->insert('third', 99);

        $this->assertEquals('first|second|third|fourth', $this->implodeQueue($queue));
    }

    private function implodeQueue(ContextDecoratorPriorityQueue $queue)
    {
        return implode('|', iterator_to_array($queue));
    }

}
