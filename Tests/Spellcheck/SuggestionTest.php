<?php

namespace Markup\NeedleBundle\Tests\Spellcheck;

use Markup\NeedleBundle\Spellcheck\Suggestion;
use PHPUnit\Framework\TestCase;

class SuggestionTest extends TestCase
{
    protected function setUp()
    {
        $this->word = 'theword';
        $this->frequency = 42;
        $this->suggestion = new Suggestion($this->word, $this->frequency);
    }

    public function testGetWord()
    {
        $this->assertEquals($this->word, $this->suggestion->getWord());
    }

    public function testCastToString()
    {
        $this->assertEquals($this->word, strval($this->suggestion));
    }

    public function testGetFrequency()
    {
        $this->assertEquals($this->frequency, $this->suggestion->getFrequency());
    }

    public function testCount()
    {
        $this->assertCount(42, $this->suggestion);
    }
}
