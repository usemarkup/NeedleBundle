<?php

namespace Markup\NeedleBundle\Tests\Indexer;

use Markup\NeedleBundle\Indexer\SubjectDataMapperProvider;

/**
* A test for a provider of subject to document data mappers.
*/
class SubjectDataMapperProviderTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->provider = new SubjectDataMapperProvider();
    }

    public function testAddAndFetchMapper()
    {
        $corpus1 = 'catalog';
        $corpus2 = 'stores';
        $catalogMapper = $this->createMock('Markup\NeedleBundle\Indexer\SubjectDataMapperInterface');
        $storesMapper = $this->createMock('Markup\NeedleBundle\Indexer\SubjectDataMapperInterface');
        $this->provider->addMapper($corpus1, $catalogMapper);
        $this->provider->addMapper($corpus2, $storesMapper);
        $this->assertSame($catalogMapper, $this->provider->fetchMapperForCorpus($corpus1));
    }

    public function testFetchUnknownMapperThrowsException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->provider->fetchMapperForCorpus('unknown');
    }
}
