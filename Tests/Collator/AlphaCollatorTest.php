<?php

namespace Markup\NeedleBundle\Tests\Collator;

use Markup\NeedleBundle\Collator\AlphaCollator;

class AlphaCollatorTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->collator = new AlphaCollator();
    }

    public function testIsTypedCollator()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Collator\TypedCollatorInterface', $this->collator);
    }

    public function testGetType()
    {
        $this->assertEquals('alpha', $this->collator->getType());
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
                ['fish', 'chips'],
                1,
            ],
            [
                ['betty', 'brush'],
                -1
            ],
        ];
    }
}
