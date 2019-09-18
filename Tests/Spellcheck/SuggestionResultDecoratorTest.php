<?php

namespace Markup\NeedleBundle\Tests\Spellcheck;

use Markup\NeedleBundle\Result\CanExposePagerfantaInterface;
use Markup\NeedleBundle\Result\ResultInterface;
use Markup\NeedleBundle\Result\SuggestionResultDecorator;
use Markup\NeedleBundle\Service\SearchServiceInterface;
use Markup\NeedleBundle\Spellcheck\SpellcheckResultInterface;
use Markup\NeedleBundle\Spellcheck\Suggestion;
use Markup\NeedleBundle\Tests\Query\SettableSelectQuery;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class SuggestionResultDecoratorTest extends MockeryTestCase
{
    /**
     * @var ResultInterface|m\MockInterface
     */
    private $originalResult;

    /**
     * @var SettableSelectQuery
     */
    private $originalQuery;

    /**
     * @var SearchServiceInterface|m\MockInterface
     */
    private $searchService;

    /**
     * @var SuggestionResultDecorator
     */
    private $decorator;

    protected function setUp()
    {
        $this->originalResult = m::mock(ResultInterface::class)->shouldIgnoreMissing();
        $this->originalQuery = new SettableSelectQuery('original query');
        $this->searchService = m::mock(SearchServiceInterface::class);
        $this->decorator = new SuggestionResultDecorator($this->originalResult, $this->originalQuery, $this->searchService);
    }

    public function testIsResult()
    {
        $this->assertInstanceOf(ResultInterface::class, $this->decorator);
    }

    public function testCanExposePagerfanta()
    {
        $this->assertInstanceOf(CanExposePagerfantaInterface::class, $this->decorator);
    }

    public function testDoNotMakeNewQueryIfNoSuggestions()
    {
        $spellcheckResult = m::mock(SpellcheckResultInterface::class)->shouldIgnoreMissing();
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
        $this->markTestSkipped('cant seem to understand this');

        $spellcheckResult = m::mock(SpellcheckResultInterface::class)->shouldIgnoreMissing();
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
