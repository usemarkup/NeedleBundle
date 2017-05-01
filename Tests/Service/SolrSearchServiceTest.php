<?php

namespace Markup\NeedleBundle\Tests\Service;

use Markup\NeedleBundle\Builder\SolariumSelectQueryBuilder;
use Markup\NeedleBundle\Service\SolrSearchService;
use Mockery as m;
use Solarium\Client;

/**
 * A test for a search service using Solr/ Solarium.
 */
class SolrSearchServiceTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->solarium = $this->createMock(Client::class);
        $this->solariumQueryBuilder = $this->createMock(SolariumSelectQueryBuilder::class);
        $this->service = new SolrSearchService($this->solarium, $this->solariumQueryBuilder);
    }

    public function tearDown()
    {
        m::close();
    }

    public function testIsSearchService()
    {
        $this->assertTrue($this->service instanceof \Markup\NeedleBundle\Service\SearchServiceInterface);
    }

    public function testExecuteQuery()
    {
        $genericQuery = $this->createMock('Markup\NeedleBundle\Query\SelectQueryInterface');
        $solariumQuery = $this->createMock('Solarium\QueryType\Select\Query\Query');
        $this->solariumQueryBuilder
            ->expects($this->atLeastOnce())
            ->method('buildSolariumQueryFromGeneric')
            ->will($this->returnValue($solariumQuery));
        $solariumResult = $this->createMock('Solarium\QueryType\Select\Result\Result');
        $this->solarium
            ->expects($this->any())
            ->method('select')
            ->will($this->returnValue($solariumResult));
        $this->assertInstanceOf('Markup\NeedleBundle\Result\ResultInterface', $this->service->executeQuery($genericQuery));
    }

    public function testCanAddDecorator()
    {
        $decorator = m::mock('Markup\NeedleBundle\Query\ResolvedSelectQueryDecoratorInterface');
        $decorated = m::mock('Markup\NeedleBundle\Query\ResolvedSelectQueryInterface');

        $decorated->shouldReceive('getSearchTerm')->andReturn('I have been decorated');
        $decorated->shouldReceive('getMaxPerPage')->andReturn(10);
        $decorated->shouldReceive('getPageNumber')->andReturn(1);
        $decorated->shouldReceive('getGroupingField')->andReturn(false);

        $decorator->shouldReceive('decorate')->andReturn($decorated);

        $this->service->addDecorator($decorator);

        $genericQuery = $this->createMock('Markup\NeedleBundle\Query\SelectQueryInterface');

        $solariumQuery = $this->createMock('Solarium\QueryType\Select\Query\Query');
        $this->solariumQueryBuilder
            ->expects($this->atLeastOnce())
            ->method('buildSolariumQueryFromGeneric')
            ->with($this->callback(function ($query) {
                return $query->getSearchTerm() ===  'I have been decorated';
            }))
            ->will($this->returnValue($solariumQuery));
        $solariumResult = $this->createMock('Solarium\QueryType\Select\Result\Result');
        $this->solarium
            ->expects($this->any())
            ->method('select')
            ->will($this->returnValue($solariumResult));
        $this->assertInstanceOf('Markup\NeedleBundle\Result\ResultInterface', $this->service->executeQuery($genericQuery));
    }
}
