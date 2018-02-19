<?php

namespace Markup\NeedleBundle\Tests\Corpus;

use Markup\NeedleBundle\Corpus\CorpusInterface;
use Markup\NeedleBundle\Corpus\CorpusProvider;
use PHPUnit\Framework\TestCase;

/**
* A test for a provider object for registered corpora.
*/
class CorpusProviderTest extends TestCase
{
    public function setUp()
    {
        $this->provider = new CorpusProvider();
    }

    public function testAddAndFetchCorpus()
    {
        $corpusName = 'corpus';
        $unknownCorpusName = 'unknown';
        $this->assertNull($this->provider->fetchNamedCorpus($corpusName));
        $corpus = $this->createMock(CorpusInterface::class);
        $this->provider->addCorpus($corpusName, $corpus);
        $this->assertSame($corpus, $this->provider->fetchNamedCorpus($corpusName));
        $this->assertNull($this->provider->fetchNamedCorpus($unknownCorpusName));
    }
}
