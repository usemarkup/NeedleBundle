<?php

namespace Markup\NeedleBundle\Tests\Indexer;

use Markup\NeedleBundle\Indexer\IndexCallbackProvider;
use Mockery as m;

class IndexCallbackProviderTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->container = m::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->provider = new IndexCallbackProvider($this->container);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testGetCallbacksForCorpusReturnsEmptyArrayByDefault()
    {
        $this->assertEquals(array(), $this->provider->getCallbacksForCorpus('unknown'));
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
        $this->provider->setCallbacksForCorpus($corpus, array($serviceId));
        $this->assertSame(array($callback), $this->provider->getCallbacksForCorpus($corpus));
    }
}
