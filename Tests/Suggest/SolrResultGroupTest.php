<?php

namespace Markup\NeedleBundle\Tests\Suggest;

use Markup\NeedleBundle\Suggest\SolrResultGroup;
use Mockery as m;

class SolrResultGroupTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->term = 'term_key';
        $this->solariumTerm = m::mock('Solarium\QueryType\Suggester\Result\Term');
        $this->group = new SolrResultGroup($this->term, $this->solariumTerm);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testGetTerm()
    {
        $this->assertEquals($this->term, $this->group->getTerm());
    }

    public function testGetDocuments()
    {
        $terms = [
            'id' => 42,
            'category' => 'shirts',
        ];
        $this->solariumTerm
            ->shouldReceive('getSuggestions')
            ->andReturn($terms);
        $documents = $this->group->getDocuments();
        $this->assertCount(1, $documents);
        $doc = $documents[0];
        $this->assertInstanceOf('Doctrine\Common\Collections\Collection', $doc);
        $this->assertEquals($terms, $doc->toArray());
    }

    public function testCount()
    {
        $count = 42;
        $this->solariumTerm
            ->shouldReceive('getNumFound')
            ->andReturn($count);
        $this->assertCount($count, $this->group);
    }

    public function testUsingRawDataTerm()
    {
        $data = [
            'numFound' => 34,
            'start' => 0,
            'docs' => [
                [
                    'id' => '511',
                    'parsed_category_en_GB' => 'FOOTWEAR',
                ],
            ],
        ];
        $group = new SolrResultGroup($this->term, $data);
        $this->assertCount(34, $group);
        $documents = $group->getDocuments();
        $this->assertCount(1, $documents);
        $doc = $documents[0];
        $this->assertInstanceOf('Doctrine\Common\Collections\Collection', $doc);
    }
}
