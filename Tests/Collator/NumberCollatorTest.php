<?php

namespace Markup\NeedleBundle\Tests\Collator;

use Markup\NeedleBundle\Collator\NumberCollator;

class NumberCollatorTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->collator = new NumberCollator();
    }

    public function testIsTypedCollator()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Collator\TypedCollatorInterface', $this->collator);
    }

    public function testHasTypeForWordIsFalse()
    {
        $this->assertFalse($this->collator->hasTypeFor('i_am_not_a_number_i_am_a_free_sentence'));
    }

    public function testHasTypeForNumericFraction()
    {
        $this->assertTrue($this->collator->hasTypeFor('1.5'));
    }

    /**
     * @dataProvider sorts
     */
    public function testCompare($subjects, $conclusion)
    {
        $this->assertEquals($conclusion, $this->collator->compare($subjects[0], $subjects[1]));
    }

    public function sorts()
    {
        return array(
            array(
                array('1.5', '2.5'),
                -1
            ),
            array(
                array('12', '2'),
                1
            )
        );
    }
}
