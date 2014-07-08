<?php

namespace Markup\NeedleBundle\Tests\Spellcheck;

use Markup\NeedleBundle\Spellcheck\StaticDictionaryProvider;

class StaticDictionaryProviderTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->dictionary = 'spell';
        $this->provider = new StaticDictionaryProvider($this->dictionary);
    }

    public function testIsDictionaryProvider()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Spellcheck\DictionaryProviderInterface', $this->provider);
    }

    public function testGetDictionary()
    {
        $this->assertEquals($this->dictionary, $this->provider->getDictionary());
    }
}
