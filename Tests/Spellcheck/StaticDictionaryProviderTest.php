<?php

namespace Markup\NeedleBundle\Tests\Spellcheck;

use Markup\NeedleBundle\Spellcheck\DictionaryProviderInterface;
use Markup\NeedleBundle\Spellcheck\StaticDictionaryProvider;
use PHPUnit\Framework\TestCase;

class StaticDictionaryProviderTest extends TestCase
{
    protected function setUp()
    {
        $this->dictionary = 'spell';
        $this->provider = new StaticDictionaryProvider($this->dictionary);
    }

    public function testIsDictionaryProvider()
    {
        $this->assertInstanceOf(DictionaryProviderInterface::class, $this->provider);
    }

    public function testGetDictionary()
    {
        $this->assertEquals($this->dictionary, $this->provider->getDictionary());
    }
}
