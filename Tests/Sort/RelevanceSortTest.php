<?php

namespace Markup\NeedleBundle\Tests\Sort;

use Markup\NeedleBundle\Sort\RelevanceSort;

/**
* A test for a sort on relevance (descending).
*/
class RelevanceSortTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->relevanceSort = new RelevanceSort();
    }

    public function testIsSort()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Sort\SortInterface', $this->relevanceSort);
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
