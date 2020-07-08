<?php

namespace Markup\NeedleBundle\Tests\Service;

use function GuzzleHttp\Promise\promise_for;
use Markup\NeedleBundle\Builder\SolariumSelectQueryBuilder;
use Markup\NeedleBundle\Facet\NoopFacetValueCanonicalizer;
use Markup\NeedleBundle\Service\SolrSearchService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Shieldo\SolariumAsyncPlugin;
use Solarium\Client;
use Markup\NeedleBundle\Query\SelectQueryInterface;
use Markup\NeedleBundle\Result\ResultInterface;
use Markup\NeedleBundle\Service\AsyncSearchServiceInterface;
use Markup\NeedleBundle\Service\SearchServiceInterface;
use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Select\Result\Result;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * A test for a search service using Solr/ Solarium.
 */
class SolrSearchServiceTest extends MockeryTestCase
{
    /**
     * @var Client|m\MockInterface
     */
    private $solarium;

    /**
     * @var SolariumSelectQueryBuilder|m\MockInterface
     */
    private $solariumQueryBuilder;

    /**
     * @var SolariumAsyncPlugin|m\MockInterface
     */
    private $promisePlugin;

    /**
     * @var SolrSearchService
     */
    private $service;

    /**
     * @var m\LegacyMockInterface|m\MockInterface|EventDispatcherInterface
     */
    private $eventDispatcher;

    protected function setUp()
    {
        $this->solarium = $this->getMockSolariumClient();
        $this->solariumQueryBuilder = m::mock(SolariumSelectQueryBuilder::class);
        $this->eventDispatcher = m::mock(EventDispatcherInterface::class);
        $this->promisePlugin = m::mock(SolariumAsyncPlugin::class);
        $this->promisePlugin
            ->shouldReceive('setEventDispatcher')
            ->andReturn(null);
        $this->promisePlugin
            ->shouldReceive('queryAsync')
            ->andReturn(promise_for(m::mock(Result::class)));
        $this->solarium
            ->shouldReceive('getPlugin')
            ->with('async')
            ->andReturn($this->promisePlugin);

        $this->service = new SolrSearchService(
            $this->solarium,
            $this->solariumQueryBuilder,
            $this->eventDispatcher,
            new NoopFacetValueCanonicalizer(),
            'corpus'
        );
    }

    public function testIsSearchService()
    {
        $this->assertInstanceOf(SearchServiceInterface::class, $this->service);
    }

    public function testIsAsync()
    {
        $this->assertInstanceOf(AsyncSearchServiceInterface::class, $this->service);
    }

    public function testExecuteQuery()
    {
        $genericQuery = m::mock(SelectQueryInterface::class)->shouldIgnoreMissing();
        $solariumQuery = m::mock(Query::class)->shouldIgnoreMissing();
        $this->solariumQueryBuilder
            ->shouldReceive('buildSolariumQueryFromGeneric')
            ->andReturn($solariumQuery);
        $solariumResult = m::mock(Result::class);
        $solariumResult->shouldReceive('createSelect');
        $this->solarium
            ->shouldReceive('createResult')
            ->andReturn($solariumResult);
        $this->assertInstanceOf(ResultInterface::class, $this->service->executeQuery($genericQuery));
    }

    private function getMockSolariumClient()
    {
        $solarium = m::mock(Client::class);
        $solarium
            ->shouldReceive('registerPlugin')
            ->andReturnSelf()
            ->shouldReceive('createSelect')
            ->andReturn(m::mock(Query::class));

        return $solarium;
    }
}
