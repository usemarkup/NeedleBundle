<?php

namespace Markup\NeedleBundle\Tests\Spellcheck;

use Markup\NeedleBundle\Result\SuggestionResultDecorator;
use Markup\NeedleBundle\Spellcheck\Suggestion;
use Markup\NeedleBundle\Tests\Query\SettableSelectQuery;
use Mockery as m;

class SuggestionResultDecoratorTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->originalResult = m::mock('Markup\NeedleBundle\Result\ResultInterface')->shouldIgnoreMissing();
        $this->originalQuery = new SettableSelectQuery('original query');
        $this->searchService = m::mock('Markup\NeedleBundle\Service\SearchServiceInterface');
        $this->decorator = new SuggestionResultDecorator($this->originalResult, $this->originalQuery, $this->searchService);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testIsResult()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Result\ResultInterface', $this->decorator);
    }

    public function testDoNotMakeNewQueryIfNoSuggestions()
    {
        $spellcheckResult = m::mock('Markup\NeedleBundle\Spellcheck\SpellcheckResultInterface')->shouldIgnoreMissing();
        $spellcheckResult
            ->shouldReceive('getSuggestions')
            ->andReturn([]);
        $this->searchService
            ->shouldReceive('executeQuery')
            ->never();
        $this->originalResult
            ->shouldReceive('getSpellcheckResult')
            ->andReturn($spellcheckResult);
        $this->originalResult
            ->shouldReceive('getIterator')
            ->andReturn(new \ArrayIterator([]));
        iterator_to_array($this->decorator);
    }

    public function testMakeNewQueryIfSuggestions()
    {
        $spellcheckResult = m::mock('Markup\NeedleBundle\Spellcheck\SpellcheckResultInterface')->shouldIgnoreMissing();
        $suggestion = new Suggestion('i am a suggestion', 42);
        $spellcheckResult
            ->shouldReceive('getSuggestions')
            ->andReturn([$suggestion]);
        $this->searchService
            ->shouldReceive('executeQuery')
            ->once();
        $this->originalResult
            ->shouldReceive('getSpellcheckResult')
            ->andReturn($spellcheckResult);
        $this->originalResult
            ->shouldReceive('getIterator')
            ->andReturn(new \ArrayIterator([]));
        $this->originalResult
            ->shouldReceive('getTotalCount')
            ->andReturn(0);
        iterator_to_array($this->decorator);
    }
}
