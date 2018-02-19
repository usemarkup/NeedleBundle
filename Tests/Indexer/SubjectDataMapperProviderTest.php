<?php

namespace Markup\NeedleBundle\Tests\Indexer;

use Markup\NeedleBundle\Indexer\SubjectDataMapperInterface;
use Markup\NeedleBundle\Indexer\SubjectDataMapperProvider;
use PHPUnit\Framework\TestCase;

/**
* A test for a provider of subject to document data mappers.
*/
class SubjectDataMapperProviderTest extends TestCase
{
    /**
     * @var SubjectDataMapperProvider
     */
    private $provider;

    protected function setUp()
    {
        $this->provider = new SubjectDataMapperProvider();
    }

    public function testAddAndFetchMapper()
    {
        $corpus1 = 'catalog';
        $corpus2 = 'stores';
        $catalogMapper = $this->createMock(SubjectDataMapperInterface::class);
        $storesMapper = $this->createMock(SubjectDataMapperInterface::class);
        $this->provider->addMapper($corpus1, $catalogMapper);
        $this->provider->addMapper($corpus2, $storesMapper);
        $this->assertSame($catalogMapper, $this->provider->fetchMapperForCorpus($corpus1));
    }

    public function testFetchUnknownMapperThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->provider->fetchMapperForCorpus('unknown');
    }
}
