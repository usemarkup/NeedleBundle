<?php
declare(strict_types=1);

use Markup\NeedleBundle\Elastic\CorpusIndexProvider;
use PHPUnit\Framework\TestCase;

class CorpusIndexProviderTest extends TestCase
{
    public function testWithoutPrefix()
    {
        $provider = new CorpusIndexProvider(null);
        $corpus = 'my_index';
        $this->assertEquals($corpus, $provider->getIndexForCorpus($corpus));
    }

    public function testWithPrefix()
    {
        $prefix = 'prefix';
        $corpus = 'corpus';
        $provider = new CorpusIndexProvider($prefix);
        $this->assertEquals('prefix_corpus', $provider->getIndexForCorpus($corpus));
    }
}
