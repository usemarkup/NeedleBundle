<?php

namespace Markup\NeedleBundle\Tests\Adapter;

use function GuzzleHttp\Promise\promise_for;
use Markup\NeedleBundle\Adapter\SolariumResultPromisePagerfantaAdapter;
use Markup\NeedleBundle\Result\ResultInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Pagerfanta\Adapter\AdapterInterface;
use Solarium\QueryType\Select\Result\Result;

class SolariumResultPromisePagerfantaAdapterTest extends MockeryTestCase
{
    /**
     * @var Result|m\MockInterface
     */
    private $result;

    /**
     * @var SolariumResultPromisePagerfantaAdapter
     */
    private $adapter;

    protected function setUp()
    {
        $this->result = m::mock(ResultInterface::class);
        $this->adapter = new SolariumResultPromisePagerfantaAdapter(promise_for($this->result));
    }

    public function testIsPagerfantaAdapter()
    {
        $this->assertInstanceOf(AdapterInterface::class, $this->adapter);
    }

    public function testGetSliceReturnsResultRegardlessOfInputs()
    {
        $this->assertSame($this->result, $this->adapter->getSlice(21, 20));
    }

    public function testGetNbResultsUsesNumFound()
    {
        $count = 42;
        $this->result
            ->shouldReceive('getNumFound')
            ->andReturn($count);
        $this->assertEquals($count, $this->adapter->getNbResults());
    }
}
