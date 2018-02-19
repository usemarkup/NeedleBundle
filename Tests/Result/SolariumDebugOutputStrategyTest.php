<?php

namespace Markup\NeedleBundle\Tests\Result;

use Markup\NeedleBundle\Context\SearchContextInterface;
use Markup\NeedleBundle\Result\DebugOutputStrategyInterface;
use Markup\NeedleBundle\Result\SolariumDebugOutputStrategy;
use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Select\Result\Result;
use Symfony\Component\Templating\EngineInterface;

/**
* A test for a debug output strategy for Solarium.
*/
class SolariumDebugOutputStrategyTest extends TestCase
{
    /**
     * @var Result
     */
    private $solariumResult;

    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var SearchContextInterface
     */
    private $searchContext;

    /**
     * @var SolariumDebugOutputStrategy
     */
    private $strategy;

    public function setUp()
    {
        $this->solariumResult = $this->createMock(Result::class);
        $this->templating = $this->createMock(EngineInterface::class);
        $this->searchContext = $this->createMock(SearchContextInterface::class);
        $this->strategy = new SolariumDebugOutputStrategy($this->solariumResult, $this->templating, $this->searchContext);
    }

    public function testIsDebugOutputStrategy()
    {
        $this->assertInstanceOf(DebugOutputStrategyInterface::class, $this->strategy);
    }

    public function testResultHasNoDebug()
    {
        $this->solariumResult
            ->expects($this->any())
            ->method('getDebug')
            ->will($this->returnValue(null));
        $this->assertFalse($this->strategy->hasDebugOutput());
        $this->assertNull($this->strategy->getDebugOutput());
    }

    public function testResultHasDebug()
    {
        $debug = $this->createMock('Solarium\QueryType\Select\Result\Debug\Result');
        $this->solariumResult
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
