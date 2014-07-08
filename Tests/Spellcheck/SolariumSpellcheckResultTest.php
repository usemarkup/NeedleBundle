<?php

namespace Markup\NeedleBundle\Tests\Spellcheck;

use Markup\NeedleBundle\Spellcheck\SolariumSpellcheckResult;
use Mockery as m;
use Solarium\QueryType\Select\Result\Spellcheck\Result;

class SolariumSpellcheckResultTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->suggestion1 = m::mock('Solarium\QueryType\Select\Result\Spellcheck\Suggestion');
        $this->suggestion2 = m::mock('Solarium\QueryType\Select\Result\Spellcheck\Suggestion');
        $this->correctlySpelled = true;
        $this->solariumResult = new Result(
            array(
                $this->suggestion1,
                $this->suggestion2,
            ),
            array(),
            $this->correctlySpelled
        );
        $this->spellcheckResult = new SolariumSpellcheckResult($this->solariumResult);
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
        $words = array('aword', 'theword');
        $this->suggestion1
            ->shouldReceive('getWords')
            ->andReturn(array($words[0]));
        $this->suggestion2
            ->shouldReceive('getWords')
            ->andReturn(array($words[1]));
        $this->assertEquals($words, $this->spellcheckResult->getSuggestions());
    }
}
