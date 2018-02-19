<?php

namespace Markup\NeedleBundle\Tests\Sort;

use Markup\NeedleBundle\Sort\RelevanceSort;
use Markup\NeedleBundle\Sort\SortInterface;
use PHPUnit\Framework\TestCase;

/**
* A test for a sort on relevance (descending).
*/
class RelevanceSortTest extends TestCase
{
    protected function setUp()
    {
        $this->relevanceSort = new RelevanceSort();
    }

    public function testIsSort()
    {
        $this->assertInstanceOf(SortInterface::class, $this->relevanceSort);
    }

    public function testFilterIsScore()
    {
        $this->assertEquals('score', $this->relevanceSort->getFilter()->getName());
    }

    public function testIsDescending()
    {
        $this->assertTrue($this->relevanceSort->isDescending());
    }
}
