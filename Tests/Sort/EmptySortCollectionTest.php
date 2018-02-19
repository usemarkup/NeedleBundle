<?php

namespace Markup\NeedleBundle\Tests\Sort;

use Markup\NeedleBundle\Sort\EmptySortCollection;
use Markup\NeedleBundle\Sort\SortCollectionInterface;
use PHPUnit\Framework\TestCase;

/**
* A test for an empty sort collection.
*/
class EmptySortCollectionTest extends TestCase
{
    protected function setUp()
    {
        $this->collection = new EmptySortCollection();
    }

    public function testIsSortCollection()
    {
        $this->assertInstanceOf(SortCollectionInterface::class, $this->collection);
    }

    public function testCountIsZero()
    {
        $this->assertCount(0, $this->collection);
    }
}
