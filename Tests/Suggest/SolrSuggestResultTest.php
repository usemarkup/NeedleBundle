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

    public function testGetGroups()
    {
        $term = m::mock('Solarium\QueryType\Suggester\Result\Term');
        $count = 42;
        $term
            ->shouldReceive('getNumFound')
            ->andReturn($count);
        $suggestions = [
            'id' => 789,
            'category_key' => 'socks',
        ];
        $term
            ->shouldReceive('getSuggestions')
            ->andReturn($suggestions);
        $termKey = 'term_key';
        $this->solrResult
            ->shouldReceive('getResults')
            ->andReturn([$termKey => $term]);
        $groups = $this->suggestResult->getGroups();
        $this->assertCount(1, $groups);
        $this->assertContainsOnlyInstancesOf('Markup\NeedleBundle\Suggest\ResultGroupInterface', $groups);
        $group = $groups[0];
        $this->assertEquals($termKey, $group->getTerm());
    }

    public function testWithRawData()
    {
        $data = [
            'matches' => 84,
            'groups' => [
                [
                    'groupValue' => 'gardener',
                    'doclist' => [
                        'numFound' => 1,
                        'start' => 0,
                        'docs' => [
                            [
                                'id' => '511',
                                'parsed_category_en_GB' => 'FOOTWEAR',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $result = new SolrSuggestResult($data);
        $this->assertCount(84, $result);
        $groups = $result->getGroups();
        $this->assertCount(1, $groups);
        $this->assertContainsOnlyInstancesOf('Markup\NeedleBundle\Suggest\ResultGroupInterface', $groups);
    }
}
