<?php

namespace Markup\NeedleBundle\Tests\Client;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Ring\Client\MockHandler;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Subscriber\Mock;
use Mockery as m;
use Markup\NeedleBundle\Client\SolrCoreAdminClient;
use Psr\Log\LoggerInterface;
use Solarium\Client as SolariumClient;
use Solarium\Core\Client\Endpoint;

class SolrCoreAdminClientTest extends \PHPUnit_Framework_TestCase
{
    public function testInstance()
    {
        $this->assertInstanceOf(SolrCoreAdminClient::class, new SolrCoreAdminClient(m::mock(SolariumClient::class)));
    }

    public function testReload()
    {
        $mockHandler = new MockHandler([
            'status' => 200,
            'body' => Stream::factory(''),
        ]);

        $guzzleClient = new GuzzleClient(['handler' => $mockHandler]);

        $endpoint = m::mock(Endpoint::class);
        $endpoint->shouldReceive('getBaseUri')->andReturn('http://i.love.solr/');
        $endpoint->shouldReceive('getCore')->andReturn('core1');

        $solariumClient = m::mock(SolariumClient::class);
        $solariumClient->shouldReceive('getEndpoint')->andReturn($endpoint);

        $client = new SolrCoreAdminClient($solariumClient, null, $guzzleClient);
        $response = $client->reload();

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testSolrClientExceptionReload()
    {
        $mockHandler = new MockHandler([
            'status' => 401,
            'body' => Stream::factory(''),
        ]);

        $guzzleClient = new GuzzleClient(['handler' => $mockHandler]);

        $endpoint = m::mock(Endpoint::class);
        $endpoint->shouldReceive('getBaseUri')->andReturn('http://i.love.solr/');
        $endpoint->shouldReceive('getCore')->andReturn('core1');

        $solariumClient = m::mock(SolariumClient::class);
        $solariumClient->shouldReceive('getEndpoint')->andReturn($endpoint);

        $logger = m::mock(LoggerInterface::class);
        $logger->shouldReceive('error')->withArgs(['Core admin operation failed using URL: http://i.love.solr/../admin/cores?action=RELOAD&core=core1']);


        $client = new SolrCoreAdminClient($solariumClient, $logger, $guzzleClient);
        $response = $client->reload();

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testSolr500ReturnReload()
    {
        $mockHandler = new MockHandler([
            'status' => 500,
            'body' => Stream::factory(''),
        ]);

        $guzzleClient = new GuzzleClient(['handler' => $mockHandler]);

        $endpoint = m::mock(Endpoint::class);
        $endpoint->shouldReceive('getBaseUri')->andReturn('http://i.love.solr/');
        $endpoint->shouldReceive('getCore')->andReturn('core1');

        $solariumClient = m::mock(SolariumClient::class);
        $solariumClient->shouldReceive('getEndpoint')->andReturn($endpoint);

        $client = new SolrCoreAdminClient($solariumClient, null, $guzzleClient);

        /**
         * For now we don't handle this.. so an exception will be thrown
         */
        $this->setExpectedException(ServerException::class);

        $client->reload();
    }
}
