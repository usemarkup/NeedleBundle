<?php

namespace Markup\NeedleBundle\Tests\Result;

use Markup\NeedleBundle\Indexer\IndexingResultInterface;
use Markup\NeedleBundle\Result\SolariumUpdateResult;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Update\Result;

class SolariumUpdateResultTest extends TestCase
{
    protected function setUp()
    {
        if (!class_exists(Result::class)) {
            $this->markTestSkipped('This test needs the solarium/solarium package in order to run.');
        }
        $this->solariumResult = m::mock(Result::class);
        $this->updateResult = new SolariumUpdateResult($this->solariumResult);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testIsUpdateResult()
    {
        $this->assertInstanceOf(IndexingResultInterface::class, $this->updateResult);
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
