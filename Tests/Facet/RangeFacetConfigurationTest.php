<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Facet\RangeFacetConfiguration;
use Markup\NeedleBundle\Facet\RangeFacetConfigurationInterface;
use PHPUnit\Framework\TestCase;

/**
* A test for a range facet configuration implementation.
*/
class RangeFacetConfigurationTest extends TestCase
{
    /**
     * @var RangeFacetConfiguration
     */
    private $config;

    protected function setUp()
    {
        $this->gap = 50;
        $this->start = 100;
        $this->end = 500;
        $this->config = new RangeFacetConfiguration($this->gap, $this->start, $this->end);
    }

    public function testIsRangeFacetConfiguration()
    {
        $this->assertInstanceOf(RangeFacetConfigurationInterface::class, $this->config);
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
