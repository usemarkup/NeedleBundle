<?php

namespace Markup\NeedleBundle\Tests\Suggest;

use Markup\NeedleBundle\Suggest\SolrSuggestResult;
use Markup\NeedleBundle\Terms\SolrTermsResult;
use Markup\NeedleBundle\Terms\TermsResultInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Solarium\QueryType\Terms\Result;

class SolrTermsResultTest extends MockeryTestCase
{
    protected function setUp()
    {
        $this->solrResult = m::mock(Result::class);
        $this->termsResult = new SolrTermsResult($this->solrResult);
    }

    public function testIsSuggestResult()
    {
        $this->assertInstanceOf(TermsResultInterface::class, $this->termsResult);
    }
}
