<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Tests\Client;

use Markup\NeedleBundle\Client\BackendClientServiceLocator;
use Markup\NeedleBundle\Client\SolrEndpointAccessor;
use Markup\NeedleBundle\Client\SolrEndpointAccessorInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Solarium\Client as SolariumClient;
use Solarium\Core\Client\Endpoint;

class SolrEndpointAccessorTest extends MockeryTestCase
{
    /**
     * @var string
     */
    private $corpus;

    /**
     * @var SolariumClient|m\MockInterface
     */
    private $solarium;

    /**
     * @var BackendClientServiceLocator
     */
    private $clientLocator;

    /**
     * @var SolrEndpointAccessor
     */
    private $accessor;

    protected function setUp()
    {
        $this->corpus = 'corpus';
        $this->solarium = m::spy(SolariumClient::class);
        $this->clientLocator = new BackendClientServiceLocator([
            $this->corpus => function () {
                return $this->solarium;
            },
        ]);
        $this->accessor = new SolrEndpointAccessor(
            $this->corpus,
            $this->clientLocator
        );
    }

    public function testIsEndpointAccessor()
    {
        $this->assertInstanceOf(SolrEndpointAccessorInterface::class, $this->accessor);
    }

    public function testGetEndpointForKey()
    {
        $key = 'iamakey';
        $endpoint = m::mock(Endpoint::class);
        $this->solarium
            ->shouldReceive('getEndpoint')
            ->andReturn($endpoint);
        $this->assertSame($endpoint, $this->accessor->getEndpointForKey($key));
    }
}
