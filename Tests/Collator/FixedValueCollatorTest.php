<?php

namespace Markup\NeedleBundle\Tests\Collator;

use Markup\NeedleBundle\Collator\FixedValueCollator;

/**
* A test for a collator that orders based on a fixed list.
*/
class FixedValueCollatorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $list = ['one', 'two', 'three', 'four', 'five'];
        $this->collator = new FixedValueCollator($list);
    }

    public function testIsCollator()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Collator\CollatorInterface', $this->collator);
    }

    /**
     * @dataProvider values
     **/
    public function testCompare($value1, $value2, $comparison)
    {
        $this->assertEquals($comparison, $this->collator->compare($value1, $value2));
    }

    public function values()
    {
        return [
            ['two', 'three', -1],
            ['four', 'three', 1],
            ['one', 'umpteen', -1],
            ['x', 'y', -1],
            ['j', 'i', 1],
        ];
    }
}
