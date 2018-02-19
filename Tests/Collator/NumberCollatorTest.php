<?php

namespace Markup\NeedleBundle\Tests\Collator;

use Markup\NeedleBundle\Collator\NumberCollator;
use Markup\NeedleBundle\Collator\TypedCollatorInterface;
use PHPUnit\Framework\TestCase;

class NumberCollatorTest extends TestCase
{
    /**
     * @var NumberCollator
     */
    private $collator;

    protected function setUp()
    {
        $this->collator = new NumberCollator();
    }

    public function testIsTypedCollator()
    {
        $this->assertInstanceOf(TypedCollatorInterface::class, $this->collator);
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
        return [
            [
                ['1.5', '2.5'],
                -1
            ],
            [
                ['12', '2'],
                1
            ]
        ];
    }
}
