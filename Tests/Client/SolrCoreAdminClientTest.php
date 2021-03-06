<?php

namespace Markup\NeedleBundle\Tests\Client;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\HandlerStack;
use Markup\NeedleBundle\Client\SolrCoreAdminClient;
use Markup\NeedleBundle\Client\SolrEndpointAccessorInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Log\LoggerInterface;
use Solarium\Client as SolariumClient;
use Solarium\Core\Client\Endpoint;

class SolrCoreAdminClientTest extends MockeryTestCase
{
    public function testInstance()
    {
        $this->assertInstanceOf(
            SolrCoreAdminClient::class,
            new SolrCoreAdminClient(m::mock(SolrEndpointAccessorInterface::class))
        );
    }

    public function testReload()
    {
        $guzzleClient = $this->createGuzzleClient(200, '');

        $endpoint = m::mock(Endpoint::class);
        $endpoint->shouldReceive('getBaseUri')->andReturn('http://i.love.solr/');
        $endpoint->shouldReceive('getCore')->andReturn('core1');

        $endpointAccessor = m::mock(SolrEndpointAccessorInterface::class)
            ->shouldReceive('getEndpointForKey')
            ->andReturn($endpoint)
            ->getMock();

        $client = new SolrCoreAdminClient($endpointAccessor, null, $guzzleClient);
        $response = $client->reload();

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testSolrClientExceptionReload()
    {
        $guzzleClient = $this->createGuzzleClient(401, '');

        $endpoint = m::mock(Endpoint::class);
        $endpoint->shouldReceive('getBaseUri')->andReturn('http://i.love.solr/');
        $endpoint->shouldReceive('getCore')->andReturn('core1');

        $endpointAccessor = m::mock(SolrEndpointAccessorInterface::class)
            ->shouldReceive('getEndpointForKey')
            ->andReturn($endpoint)
            ->getMock();

        $logger = m::mock(LoggerInterface::class);
        $logger->shouldReceive('error')
            ->withArgs(
                ['Core admin operation failed using URL: http://i.love.solr/../admin/cores?action=RELOAD&core=core1']
            );

        $client = new SolrCoreAdminClient($endpointAccessor, $logger, $guzzleClient);
        $response = $client->reload();

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testSolr500ReturnReload()
    {
        $guzzleClient = $this->createGuzzleClient(500, '');

        $endpoint = m::mock(Endpoint::class);
        $endpoint->shouldReceive('getBaseUri')->andReturn('http://i.love.solr/');
        $endpoint->shouldReceive('getCore')->andReturn('core1');

        $endpointAccessor = m::mock(SolrEndpointAccessorInterface::class)
            ->shouldReceive('getEndpointForKey')
            ->andReturn($endpoint)
            ->getMock();

        $client = new SolrCoreAdminClient($endpointAccessor, null, $guzzleClient);

        /**
         * For now we don't handle this.. so an exception will be thrown
         */
        $this->expectException(ServerException::class);

        $client->reload();
    }

    private function createGuzzleClient($statusCode, $body)
    {
        $handler = HandlerStack::create(
            new \GuzzleHttp\Handler\MockHandler([
                new \GuzzleHttp\Psr7\Response($statusCode, [], \GuzzleHttp\Psr7\stream_for($body))
            ])
        );

        return new GuzzleClient(['handler' => $handler]);
    }
}
