<?php

namespace Markup\NeedleBundle\Tests\Sort;

use Markup\NeedleBundle\Sort\Sort;

/**
* A test for a simple sort implementation.
*/
class SortTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->filter = $this->createMock('Markup\NeedleBundle\Attribute\AttributeInterface');
        $this->sort = new Sort($this->filter);
    }

    public function testIsSort()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Sort\SortInterface', $this->sort);
    }

    public function testGetFilter()
    {
        $this->assertSame($this->filter, $this->sort->getFilter());
    }

    public function testIsNotDescendingByDefault()
    {
        $this->assertFalse($this->sort->isDescending());
    }

    public function testIsDescendingSetFromConstructor()
    {
        $sort = new Sort($this->filter, true);
        $this->assertTrue($sort->isDescending());
    }
}
