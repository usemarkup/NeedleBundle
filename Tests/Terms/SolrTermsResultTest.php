<?php

namespace Markup\NeedleBundle\Tests\Suggest;

use Markup\NeedleBundle\Suggest\SolrSuggestResult;
use Markup\NeedleBundle\Terms\SolrTermsResult;
use Mockery as m;

class SolrSuggestResultTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->solrResult = m::mock('Solarium\QueryType\Terms\Result');
        $this->termsResult = new SolrTermsResult($this->solrResult);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testIsSuggestResult()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Terms\TermsResultInterface', $this->termsResult);
    }
}
