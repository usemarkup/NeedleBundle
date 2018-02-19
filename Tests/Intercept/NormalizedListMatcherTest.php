<?php

namespace Markup\NeedleBundle\Tests\Intercept;

use Markup\NeedleBundle\Intercept\MatcherInterface;
use Markup\NeedleBundle\Intercept\NormalizedListMatcher;
use PHPUnit\Framework\TestCase;

/**
* Test for a matcher that takes a list of exact terms, though matched case-insensitively and with variant characters normalized.
*/
class NormalizedListMatcherTest extends TestCase
{
    /**
     * @var NormalizedListMatcher
     */
    private $matcher;

    protected function setUp()
    {
        $this->matcher = new NormalizedListMatcher();
    }

    public function testIsMatcher()
    {
        $this->assertInstanceOf(MatcherInterface::class, $this->matcher);
    }

    public function testDoesNotMatchWithEmptyList()
    {
        $query = 'jumpers';
        $this->assertFalse($this->matcher->matches($query));
    }

    /**
     * @dataProvider lists
     **/
    public function testMatches($query, $list, $whether)
    {
        $this->matcher->setList($list);
        $this->assertEquals($whether, $this->matcher->matches($query));
    }

    public function lists()
    {
        return [
            ['jumpers', ['JUMPERS', 'jumper', 'sweater'], true],
            ['cabbage', ['lettuce', 'courgette'], false],
            ['STRAßE', ['straße', 'allee'], true],
            ['ÜBER', ['uber', 'unter'], true],
        ];
    }
}
