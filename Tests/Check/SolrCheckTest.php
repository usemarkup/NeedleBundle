<?php

namespace Markup\NeedleBundle\Tests\Check;

use Markup\NeedleBundle\Check\SolrCheck;
use PHPUnit\Framework\TestCase;
use Solarium\Client;
use Solarium\Core\Query\QueryInterface;
use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\FailureInterface;
use ZendDiagnostics\Result\SuccessInterface;

/**
* A test for a monitor check object that can check whether a Solr instance is available.
*/
class SolrCheckTest extends TestCase
{
    /**
     * @var Client
     */
    private $solariumClient;

    /**
     * @var SolrCheck
     */
    private $check;

    public function setUp()
    {
        $this->solariumClient = $this->createMock(Client::class);
        $this->check = new SolrCheck($this->solariumClient);
    }

    public function testIsCheck()
    {
        $this->assertInstanceOf(CheckInterface::class, $this->check);
    }

    public function testCheckWhenOK()
    {
        $solrPing = $this->createMock(QueryInterface::class);
        $this->solariumClient
            ->expects($this->any())
            ->method('createPing')
            ->will($this->returnValue($solrPing));
        $result = 'OK';
        $this->solariumClient
            ->expects($this->any())
            ->method('ping')
            ->with($this->equalTo($solrPing))
            ->will($this->returnValue($result));
        $checkResult = $this->check->check();
        $this->assertInstanceOf(SuccessInterface::class, $checkResult);
    }

    public function testCheckWhenFail()
    {
        $solrPing = $this->createMock(QueryInterface::class);
        $this->solariumClient
            ->expects($this->any())
            ->method('createPing')
            ->will($this->returnValue($solrPing));
        $this->solariumClient
            ->expects($this->any())
            ->method('ping')
            ->with($this->equalTo($solrPing))
            ->will($this->throwException(new \Solarium\Exception\RuntimeException()));
        $checkResult = $this->check->check();
        $this->assertInstanceOf(FailureInterface::class, $checkResult);
    }
}
