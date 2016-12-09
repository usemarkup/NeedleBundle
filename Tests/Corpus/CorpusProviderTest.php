<?php

namespace Markup\NeedleBundle\Tests\Corpus;

use Markup\NeedleBundle\Corpus\CorpusProvider;

/**
* A test for a provider object for registered corpora.
*/
class CorpusProviderTest extends \PHPUnit_Framework_TestCase
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
        $corpus = $this->createMock('Markup\NeedleBundle\Corpus\CorpusInterface');
        $this->provider->addCorpus($corpusName, $corpus);
        $this->assertSame($corpus, $this->provider->fetchNamedCorpus($corpusName));
        $this->assertNull($this->provider->fetchNamedCorpus($unknownCorpusName));
    }
}
