<?php

namespace Markup\NeedleBundle\Tests\Suggest;

use Markup\NeedleBundle\Suggest\SolrSuggestService;
use Mockery as m;

class SolrSuggestServiceTest extends \PHPUnit_Framework_TestCase
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
        $this->assertInstanceOf('Markup\NeedleBundle\Suggest\SuggestServiceInterface', $this->suggester);
    }
}
