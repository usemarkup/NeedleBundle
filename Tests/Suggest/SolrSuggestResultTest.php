<?php

namespace Markup\NeedleBundle\Tests\Suggest;

use Markup\NeedleBundle\Suggest\SolrSuggestResult;
use Mockery as m;

class SolrSuggestResultTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->solrResult = m::mock('Solarium\QueryType\Suggester\Result\Result');
        $this->suggestResult = new SolrSuggestResult($this->solrResult);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testIsSuggestResult()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Suggest\SuggestResultInterface', $this->suggestResult);
    }

    public function testCount()
    {
        $count = 42;
        $this->solrResult
            ->shouldReceive('count')
            ->andReturn($count);
        $this->assertEquals($count, count($this->suggestResult));
    }

    public function testGetSuggestions()
    {
        $suggestions = array('talk', 'talking');
        $term = 'term';
        $this->solrResult
            ->shouldReceive('getResults')
            ->andReturn(array($term => $suggestions));
        $this->assertEquals($suggestions, $this->suggestResult->getSuggestions());
    }
}
