<?php

namespace Tests\Indexer;

use Elasticsearch\Client;
use Markup\NeedleBundle\Indexer\ElasticsearchIndexingMessager;
use Markup\NeedleBundle\Indexer\IndexingMessagerInterface;
use Markup\NeedleBundle\Indexer\SubjectDataMapperProvider;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class ElasticsearchIndexingMessagerTest extends MockeryTestCase
{
    /**
     * @var Client|m\MockInterface
     */
    private $elastic;

    /**
     * @var SubjectDataMapperProvider|m\MockInterface
     */
    private $subjectDataMapperProvider;

    /**
     * @var ElasticsearchIndexingMessager
     */
    private $messager;

    protected function setUp()
    {
        $this->elastic = m::mock(Client::class);
        $this->subjectDataMapperProvider = m::mock(SubjectDataMapperProvider::class);
        $this->messager = new ElasticsearchIndexingMessager(
            $this->elastic,
            $this->subjectDataMapperProvider
        );
    }

    public function testIsIndexingMessager()
    {
        $this->assertInstanceOf(IndexingMessagerInterface::class, $this->messager);
    }
}
