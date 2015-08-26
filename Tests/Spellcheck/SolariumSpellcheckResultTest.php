<?php

namespace Markup\NeedleBundle\Tests\Spellcheck;

use Markup\NeedleBundle\Spellcheck\SolariumSpellcheckResult;
use Mockery as m;
use Solarium\QueryType\Select\Result\Spellcheck\Result;

class SolariumSpellcheckResultTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->suggestion1 = m::mock('Solarium\QueryType\Select\Result\Spellcheck\Suggestion')->shouldIgnoreMissing();
        $this->suggestion2 = m::mock('Solarium\QueryType\Select\Result\Spellcheck\Suggestion')->shouldIgnoreMissing();
        $this->correctlySpelled = true;
        $this->solariumResult = new Result(
            [
                $this->suggestion1,
                $this->suggestion2,
            ],
            [],
            $this->correctlySpelled
        );
        $this->query = m::mock('Markup\NeedleBundle\Query\SimpleQueryInterface')->shouldIgnoreMissing();
        $this->spellcheckResult = new SolariumSpellcheckResult($this->solariumResult, $this->query);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testIsCorrectlySpelled()
    {
        $this->assertTrue($this->spellcheckResult->isCorrectlySpelled());
    }

    public function testGetSuggestions()
    {
        $words = ['aword', 'theword'];
        $this->suggestion1
            ->shouldReceive('getWord')
            ->andReturn($words[0]);
        $this->suggestion2
            ->shouldReceive('getWord')
            ->andReturn($words[1]);
        $suggestions = $this->spellcheckResult->getSuggestions();
        $this->assertContainsOnlyInstancesOf('Markup\NeedleBundle\Spellcheck\Suggestion', $suggestions);
        $this->assertEquals($words, $suggestions);
    }

    public function testGetSuggestionsReducedByOriginalQuery()
    {
        $words = ['aword', 'theword'];
        $this->suggestion1
            ->shouldReceive('getWord')
            ->andReturn($words[0]);
        $this->suggestion2
            ->shouldReceive('getWord')
            ->andReturn($words[1]);
        $this->query
            ->shouldReceive('hasSearchTerm')
            ->andReturn(true);
        $this->query
            ->shouldReceive('getSearchTerm')
            ->andReturn('aword');
        $this->assertEquals(['theword'], $this->spellcheckResult->getSuggestions());
    }
}
