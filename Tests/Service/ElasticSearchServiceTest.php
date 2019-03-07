<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Tests\Service;

use Elasticsearch\Client;
use Markup\NeedleBundle\Builder\ElasticSelectQueryBuilder;
use Markup\NeedleBundle\Builder\QueryBuildOptionsLocator;
use Markup\NeedleBundle\Service\AsyncSearchServiceInterface;
use Markup\NeedleBundle\Service\ElasticSearchService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class ElasticSearchServiceTest extends MockeryTestCase
{
    /**
     * @var Client|m\MockInterface
     */
    private $client;

    /**
     * @var ElasticSelectQueryBuilder|m\MockInterface
     */
    private $queryBuilder;

    /**
     * @var ElasticSearchService
     */
    private $search;

    protected function setUp()
    {
        $this->client = m::mock(Client::class);
        $this->queryBuilder = m::mock(ElasticSelectQueryBuilder::class);
        $this->search = new ElasticSearchService(
            $this->client,
            $this->queryBuilder,
            new QueryBuildOptionsLocator([]),
            'corpus'
        );
    }

    public function testIsAsyncSearchService()
    {
        $this->assertInstanceOf(AsyncSearchServiceInterface::class, $this->search);
    }
}
