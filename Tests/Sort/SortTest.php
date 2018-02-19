<?php

namespace Markup\NeedleBundle\Tests\Sort;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Sort\Sort;
use Markup\NeedleBundle\Sort\SortInterface;
use PHPUnit\Framework\TestCase;

/**
* A test for a simple sort implementation.
*/
class SortTest extends TestCase
{
    /**
     * @var AttributeInterface
     */
    private $filter;

    /**
     * @var Sort
     */
    private $sort;

    protected function setUp()
    {
        $this->filter = $this->createMock(AttributeInterface::class);
        $this->sort = new Sort($this->filter);
    }

    public function testIsSort()
    {
        $this->assertInstanceOf(SortInterface::class, $this->sort);
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
