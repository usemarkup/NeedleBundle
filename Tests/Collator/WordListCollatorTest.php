<?php

namespace Markup\NeedleBundle\Tests\Collator;

use Markup\NeedleBundle\Collator\WordListCollator;

/**
 * Test for a collator that uses a word list using known words.
 */
class WordListCollatorTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->wordList = array(
            'the',
            'quick',
            'brown',
            'fox',
            'jumped',
            'over',
            'the',
            'lazy',
            'dog',
        );
        $this->collator = new WordListCollator($this->wordList);
    }

    public function testIsTypedCollator()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Collator\TypedCollatorInterface', $this->collator);
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
        return array(
            array('the', true),
            array('quick', true),
            array('green', false),
            array('fox', true),
            array('jumped', true),
            array('over', true),
            array('energetic', false),
            array('cat', false),
        );
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
        return array(
            array('the', 'quick', -1),
            array('lazy', 'brown', 1),
        );
    }
}
