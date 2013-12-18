<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Facet\RangeFacetConfiguration;

/**
* A test for a range facet configuration implementation.
*/
class RangeFacetConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->gap = 50;
        $this->start = 100;
        $this->end = 500;
        $this->config = new RangeFacetConfiguration($this->gap, $this->start, $this->end);
    }

    public function testIsRangeFacetConfiguration()
    {
        $this->assertTrue($this->config instanceof \Markup\NeedleBundle\Facet\RangeFacetConfigurationInterface);
    }

    public function testGetStart()
    {
        $this->assertSame($this->start, $this->config->getStart());
    }

    public function testGetEnd()
    {
        $this->assertSame($this->end, $this->config->getEnd());
    }

    public function testGetGap()
    {
        $this->assertSame($this->gap, $this->config->getGap());
    }
}
