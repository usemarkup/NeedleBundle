<?php

namespace Markup\NeedleBundle\Tests\Indexer;

use Markup\NeedleBundle\Indexer\IndexCallbackProvider;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class IndexCallbackProviderTest extends MockeryTestCase
{
    /**
     * @var ContainerInterface|m\MockInterface
     */
    private $container;

    /**
     * @var IndexCallbackProvider
     */
    private $provider;

    protected function setUp()
    {
        $this->container = m::mock(ContainerInterface::class);
        $this->provider = new IndexCallbackProvider($this->container);
    }

    public function testGetCallbacksForCorpusReturnsEmptyArrayByDefault()
    {
        $this->assertEquals([], $this->provider->getCallbacksForCorpus('unknown'));
    }

    public function testGetCallbacksForProviderCorpus()
    {
        $callback = function () {};
        $serviceId = 'callback';
        $this->container
            ->shouldReceive('get')
            ->with($serviceId)
            ->andReturn($callback);
        $corpus = 'corpus';
        $this->provider->setCallbacksForCorpus($corpus, [$serviceId]);
        $this->assertSame([$callback], $this->provider->getCallbacksForCorpus($corpus));
    }
}
