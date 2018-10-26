<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Tests\Indexer;

use Markup\NeedleBundle\Indexer\IndexingResult;
use Markup\NeedleBundle\Indexer\IndexingResultInterface;
use PHPUnit\Framework\TestCase;

class IndexingResultTest extends TestCase
{
    /**
     * @var IndexingResult
     */
    private $result;

    protected function setUp()
    {
        $this->result = new IndexingResult(
            true,
            200,
            42,
            'searchy'
        );
    }

    public function testIsIndexingResult()
    {
        $this->assertInstanceOf(IndexingResultInterface::class, $this->result);
    }

    public function testIsSuccessful()
    {
        $this->assertTrue($this->result->isSuccessful());
    }

    public function testGetStatusCode()
    {
        $this->assertEquals(200, $this->result->getStatusCode());
    }

    public function testGetQueryTime()
    {
        $this->assertEquals(42, $this->result->getQueryTimeInMilliseconds());
    }

    public function testGetBackendSoftware()
    {
        $this->assertEquals('searchy', $this->result->getBackendSoftware());
    }
}
