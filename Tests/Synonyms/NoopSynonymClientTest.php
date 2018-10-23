<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Tests\Synonyms;

use Markup\NeedleBundle\Synonyms\NoopSynonymClient;
use Markup\NeedleBundle\Synonyms\SynonymClientInterface;
use PHPUnit\Framework\TestCase;

class NoopSynonymClientTest extends TestCase
{
    /**
     * @var NoopSynonymClient
     */
    private $client;

    protected function setUp()
    {
        $this->client = new NoopSynonymClient();
    }

    public function testIsSynonymClient()
    {
        $this->assertInstanceOf(SynonymClientInterface::class, $this->client);
    }

    public function testGetStoredLocalesReturnsEmptyList()
    {
        $this->assertEquals([], $this->client->getStoredLocales());
    }

    public function testGetSynonymsReturnsEmptyList()
    {
        $this->assertEquals([], $this->client->getSynonyms('en'));
    }

    public function testUpdateSynonymsFails()
    {
        $this->assertFalse($this->client->updateSynonyms('en', []));
    }
}
