<?php

namespace Markup\NeedleBundle\Tests\Result;

use Markup\NeedleBundle\Result\SolariumDebugOutputStrategy;

/**
* A test for a debug output strategy for Solarium.
*/
class SolariumDebugOutputStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->result = $this->getMockBuilder('Solarium\QueryType\Select\Result\Result')
            ->disableOriginalConstructor()
            ->getMock();
        $this->templating = $this->createMock('Symfony\Component\Templating\EngineInterface');
        $this->searchContext = $this->createMock('Markup\NeedleBundle\Context\SearchContextInterface');
        $this->strategy = new SolariumDebugOutputStrategy($this->result, $this->templating, $this->searchContext);
    }

    public function testIsDebugOutputStrategy()
    {
        $this->assertTrue($this->strategy instanceof \Markup\NeedleBundle\Result\DebugOutputStrategyInterface);
    }

    public function testResultHasNoDebug()
    {
        $this->result
            ->expects($this->any())
            ->method('getDebug')
            ->will($this->returnValue(null));
        $this->assertFalse($this->strategy->hasDebugOutput());
        $this->assertNull($this->strategy->getDebugOutput());
    }

    public function testResultHasDebug()
    {
        $debug = $this->getMockBuilder('Solarium\QueryType\Select\Result\Debug\Result')
            ->disableOriginalConstructor()
            ->getMock();
        $this->result
            ->expects($this->any())
            ->method('getDebug')
            ->will($this->returnValue($debug));
        $this->assertTrue($this->strategy->hasDebugOutput());
        $output = 'that was sloooooow!';
        $this->templating
            ->expects($this->any())
            ->method('render')
            ->with($this->isType('string'), ['debug' => $debug])
            ->will($this->returnValue($output));
        $this->assertEquals($output, $this->strategy->getDebugOutput());
    }
}
