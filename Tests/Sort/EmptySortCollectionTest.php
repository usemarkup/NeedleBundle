<?php

namespace Markup\NeedleBundle\Tests\Sort;

use Markup\NeedleBundle\Sort\EmptySortCollection;

/**
* A test for an empty sort collection.
*/
class EmptySortCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->collection = new EmptySortCollection();
    }

    public function testIsSortCollection()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Sort\SortCollectionInterface', $this->collection);
    }

    public function testCountIsZero()
    {
        $this->assertCount(0, $this->collection);
    }
}
