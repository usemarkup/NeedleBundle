<?php

namespace Markup\NeedleBundle\Tests\Suggest;

use Markup\NeedleBundle\Suggest\SolrSuggestService;
use Markup\NeedleBundle\Suggest\SuggestServiceInterface;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class SolrSuggestServiceTest extends TestCase
{
    protected function setUp()
    {
        $this->solarium = m::mock('Solarium\Client');
        $this->suggester = new SolrSuggestService($this->solarium);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testIsSuggestService()
    {
        $this->assertInstanceOf(SuggestServiceInterface::class, $this->suggester);
    }
}
