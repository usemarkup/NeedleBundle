<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Tests\Adapter;

use function GuzzleHttp\Promise\promise_for;
use Markup\NeedleBundle\Adapter\ElasticResultPromisePagerfantaAdapter;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Pagerfanta\Adapter\AdapterInterface;

class ElasticResultPromisePagerfantaAdapterTest extends MockeryTestCase
{
    /**
     * @var array
     */
    private $result;

    /**
     * @var ElasticResultPromisePagerfantaAdapter
     */
    private $adapter;

    protected function setUp()
    {
        $this->result = [
            'took' => 1,
            'hits' => [
                'hits' => [
                    [
                        '_source' => [
                            'id' => 42,
                        ],
                    ],
                    [
                        '_source' => [
                            'id' => 43,
                        ],
                    ],
                ],
            ],
        ];
        $this->adapter = new ElasticResultPromisePagerfantaAdapter(promise_for($this->result));
    }

    public function testIsPagerfantaAdapter()
    {
        $this->assertInstanceOf(AdapterInterface::class, $this->adapter);
    }

    public function testGetSliceReturnsResultRegardlessOfInputs()
    {
        $expectedSlice = [
            (object) ['id' => 42],
            (object) ['id' => 43],
        ];
        $this->assertEquals($expectedSlice, $this->adapter->getSlice(21, 20));
    }

    public function testGetNbResultsUsesHitsTotal()
    {
        $count = 42;
        $result = [
            'hits' => [
                'total' => $count,
            ],
        ];
        $adapter = new ElasticResultPromisePagerfantaAdapter(promise_for($result));
        $this->assertEquals($count, $adapter->getNbResults());
    }
}
