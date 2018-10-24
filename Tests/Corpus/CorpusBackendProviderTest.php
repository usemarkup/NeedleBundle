<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Tests\Corpus;

use Markup\NeedleBundle\Corpus\CorpusBackendProvider;
use PHPUnit\Framework\TestCase;

class CorpusBackendProviderTest extends TestCase
{
    /**
     * @var CorpusBackendProvider
     */
    private $provider;

    protected function setUp()
    {
        $this->provider = new CorpusBackendProvider(['corpus' => 'backend']);
    }

    public function testGetBackendForCorpus()
    {
        $this->assertEquals('backend', $this->provider->getBackendForCorpus('corpus'));
    }

    public function testGetBackendForCorpusWithUnknownCorpus()
    {
        $this->expectException(\OutOfRangeException::class);
        $this->provider->getBackendForCorpus('unknown');
    }
}
