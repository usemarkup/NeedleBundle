<?php

namespace Markup\NeedleBundle\Tests\Client;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\HandlerStack;
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
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function testInstance()
    {
        $this->assertInstanceOf(SolrCoreAdminClient::class, new SolrCoreAdminClient(m::mock(SolariumClient::class)));
    }

    public function testReload()
    {
        $guzzleClient = $this->createGuzzleClient(200, '');

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
        $guzzleClient = $this->createGuzzleClient(401, '');

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
        $guzzleClient = $this->createGuzzleClient(500, '');

        $endpoint = m::mock(Endpoint::class);
        $endpoint->shouldReceive('getBaseUri')->andReturn('http://i.love.solr/');
        $endpoint->shouldReceive('getCore')->andReturn('core1');

        $solariumClient = m::mock(SolariumClient::class);
        $solariumClient->shouldReceive('getEndpoint')->andReturn($endpoint);

        $client = new SolrCoreAdminClient($solariumClient, null, $guzzleClient);

        /**
         * For now we don't handle this.. so an exception will be thrown
         */
        $this->expectException(ServerException::class);

        $client->reload();
    }

    private function createGuzzleClient($statusCode, $body)
    {
        $usingAtLeastGuzzle6 = version_compare(ClientInterface::VERSION, '6.0.0', '>=');
        if ($usingAtLeastGuzzle6) {
            $handler = HandlerStack::create(
                new \GuzzleHttp\Handler\MockHandler([
                    new \GuzzleHttp\Psr7\Response($statusCode, [], \GuzzleHttp\Psr7\stream_for($body))
                ])
            );
        } else {
            $handler = new MockHandler([
                'status' => $statusCode,
                'body' => Stream::factory($body),
            ]);
        }

        return new GuzzleClient(['handler' => $handler]);
    }
}
