<?php

namespace Markup\NeedleBundle\Tests\Indexer;

use Markup\NeedleBundle\Indexer\IndexCallbackProvider;
use PHPUnit\Framework\TestCase;

class IndexCallbackProviderTest extends TestCase
{
    /**
     * @var IndexCallbackProvider
     */
    private $provider;

    protected function setUp()
    {
        $this->provider = new IndexCallbackProvider([]);
    }

    public function testGetCallbacksForCorpusReturnsEmptyArrayByDefault()
    {
        $this->assertEquals([], $this->provider->getCallbacksForCorpus('unknown'));
    }

    public function testGetCallbacksForProviderCorpus()
    {
        $callback = function () {};
        $serviceCollection = new \ArrayIterator([$callback]);
        $corpus = 'corpus';
        $provider = new IndexCallbackProvider([
            $corpus => $serviceCollection,
        ]);
        $this->assertSame([$callback], iterator_to_array($provider->getCallbacksForCorpus($corpus)));
    }
}
