<?php

namespace Markup\NeedleBundle\Tests\Service;

use Markup\NeedleBundle\Service\SolrSearchService;

/**
* A test for a search service using Solr/ Solarium.
*/
class SolrSearchServiceTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->solarium = $this->getMockBuilder('Solarium\Client')
            ->disableOriginalConstructor()
            ->getMock();
        $this->solariumQueryBuilder = $this->getMockBuilder('Markup\NeedleBundle\Builder\SolariumSelectQueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->service = new SolrSearchService($this->solarium, $this->solariumQueryBuilder);
    }

    public function testIsSearchService()
    {
        $this->assertTrue($this->service instanceof \Markup\NeedleBundle\Service\SearchServiceInterface);
    }

    public function testExecuteQuery()
    {
        $genericQuery = $this->getMock('Markup\NeedleBundle\Query\SelectQueryInterface');
        $solariumQuery = $this->getMockBuilder('Solarium\QueryType\Select\Query\Query')
            ->disableOriginalConstructor()
            ->getMock();
        $this->solariumQueryBuilder
            ->expects($this->atLeastOnce())
            ->method('buildSolariumQueryFromGeneric')
            ->will($this->returnValue($solariumQuery));
        $solariumResult = $this->getMockBuilder('Solarium\QueryType\Select\Result\Result')
            ->disableOriginalConstructor()
            ->getMock();
        $this->solarium
            ->expects($this->any())
            ->method('select')
            ->will($this->returnValue($solariumResult));
        $this->assertInstanceOf('Markup\NeedleBundle\Result\ResultInterface', $this->service->executeQuery($genericQuery));
    }
}
