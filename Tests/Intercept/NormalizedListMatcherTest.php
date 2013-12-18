<?php

namespace Markup\NeedleBundle\Tests\Intercept;

use Markup\NeedleBundle\Intercept\NormalizedListMatcher;

/**
* Test for a matcher that takes a list of exact terms, though matched case-insensitively and with variant characters normalized.
*/
class NormalizedListMatcherTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->matcher = new NormalizedListMatcher();
    }

    public function testIsMatcher()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Intercept\MatcherInterface', $this->matcher);
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
        return array(
            array('jumpers', array('JUMPERS', 'jumper', 'sweater'), true),
            array('cabbage', array('lettuce', 'courgette'), false),
            array('STRAßE', array('straße', 'allee'), true),
            array('ÜBER', array('uber', 'unter'), true),
        );
    }
}
