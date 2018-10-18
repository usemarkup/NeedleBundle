<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Tests\Client;

use Markup\NeedleBundle\Client\SingleClientEndpointAccessor;
use Markup\NeedleBundle\Client\SolrEndpointAccessorInterface;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Solarium\Client;
use Solarium\Core\Client\Endpoint;

class SingleClientEndpointAccessorTest extends TestCase
{
    /**
     * @var Client|m\MockInterface
     */
    private $solarium;

    /**
     * @var SingleClientEndpointAccessor
     */
    private $accessor;

    protected function setUp()
    {
        $this->solarium = m::mock(Client::class);
        $this->accessor = new SingleClientEndpointAccessor($this->solarium);
    }

    public function testIsEndpointAccessor()
    {
        $this->assertInstanceOf(SolrEndpointAccessorInterface::class, $this->accessor);
    }

    public function testGetEndpointForKey()
    {
        $endpointKey = 'endpoint';
        $endpoint = m::mock(Endpoint::class);
        $this->solarium
            ->shouldReceive('getEndpoint')
            ->with($endpointKey)
            ->andReturn($endpoint);
        $this->assertEquals($endpoint, $this->accessor->getEndpointForKey($endpointKey));
    }
}
