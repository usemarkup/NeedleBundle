<?php

namespace Markup\NeedleBundle\Tests\Collator;

use Markup\NeedleBundle\Collator\TypedCollatorInterface;
use Markup\NeedleBundle\Collator\WordListCollator;
use PHPUnit\Framework\TestCase;

/**
 * Test for a collator that uses a word list using known words.
 */
class WordListCollatorTest extends TestCase
{
    protected function setUp()
    {
        $this->wordList = [
            'the',
            'quick',
            'brown',
            'fox',
            'jumped',
            'over',
            'the',
            'lazy',
            'dog',
        ];
        $this->collator = new WordListCollator($this->wordList);
    }

    public function testIsTypedCollator()
    {
        $this->assertInstanceOf(TypedCollatorInterface::class, $this->collator);
    }

    /**
     * @dataProvider values
     */
    public function testHasTypeFor($value, $whether)
    {
        $this->assertEquals($whether, $this->collator->hasTypeFor($value));
    }

    public function values()
    {
        return [
            ['the', true],
            ['quick', true],
            ['green', false],
            ['fox', true],
            ['jumped', true],
            ['over', true],
            ['energetic', false],
            ['cat', false],
        ];
    }

    /**
     * @dataProvider comparisons
     **/
    public function testCompare($value1, $value2, $expected)
    {
        $this->assertEquals($expected, $this->collator->compare($value1, $value2));
    }

    public function comparisons()
    {
        return [
            ['the', 'quick', -1],
            ['lazy', 'brown', 1],
        ];
    }
}
