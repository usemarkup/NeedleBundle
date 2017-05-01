<?php

namespace Markup\NeedleBundle\Tests\Result;

use Markup\NeedleBundle\Result\QueryTimeStrategyInterface;
use Markup\NeedleBundle\Result\SolariumQueryTimeStrategy;

/**
* A test for a strategy for fetching a query time that uses a Solarium result.
*/
class SolariumQueryTimeStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->solariumResult = $this->createMock('Solarium\QueryType\Select\Result\Result');
        $this->strategy = new SolariumQueryTimeStrategy($this->solariumResult);
    }

    public function testIsQueryTimeStrategy()
    {
        $this->assertInstanceOf(QueryTimeStrategyInterface::class, $this->strategy);
    }

    public function testGetQueryTimeInMilliseconds()
    {
        $solariumQueryTime = 42;
        $this->solariumResult
            ->expects($this->atLeastOnce())
            ->method('getQueryTime')
            ->will($this->returnValue($solariumQueryTime));
        $actualTime = $this->strategy->getQueryTimeInMilliseconds();
        $this->assertEquals($solariumQueryTime, $actualTime);
        $this->assertInternalType('float', $actualTime);
    }
}
