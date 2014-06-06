<?php

namespace Markup\NeedleBundle\Tests\Result;

use Markup\NeedleBundle\Result\SolariumUpdateResult;
use Mockery as m;

class SolariumUpdateResultTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('Solarium\QueryType\Update\Result')) {
            $this->markTestSkipped('This test needs the solarium/solarium package in order to run.');
        }
        $this->solariumResult = m::mock('Solarium\QueryType\Update\Result');
        $this->updateResult = new SolariumUpdateResult($this->solariumResult);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testIsUpdateResult()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Result\UpdateResultInterface', $this->updateResult);
    }

    public function testGetStatusCodeReturnsStatus()
    {
        $status = 42;
        $this->solariumResult
            ->shouldReceive('getStatus')
            ->andReturn($status);
        $this->assertEquals($status, $this->updateResult->getStatusCode());
    }

    public function testGetQueryTimeInMilliseconds()
    {
        $ms = 702;
        $this->solariumResult
            ->shouldReceive('getQueryTime')
            ->andReturn($ms);
        $this->assertEquals($ms, $this->updateResult->getQueryTimeInMilliseconds());
    }

    public function testIsSuccessfulForZeroStatus()
    {
        $status = 0;
        $this->solariumResult
            ->shouldReceive('getStatus')
            ->andReturn($status);
        $this->assertTrue($this->updateResult->isSuccessful());
    }

    public function testIsNotSuccessfulForNonZeroStatus()
    {
        $status = 42;
        $this->solariumResult
            ->shouldReceive('getStatus')
            ->andReturn($status);
        $this->assertFalse($this->updateResult->isSuccessful());
    }
}
