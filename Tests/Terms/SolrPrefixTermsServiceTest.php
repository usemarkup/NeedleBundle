<?php

namespace Markup\NeedleBundle\Tests\Terms;

use Markup\NeedleBundle\Terms\SolrPrefixTermsService;
use Markup\NeedleBundle\Terms\TermsFieldProviderInterface;
use Markup\NeedleBundle\Terms\TermsServiceInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class SolrPrefixTermsServiceTest extends MockeryTestCase
{
    protected function setUp()
    {
        $this->solarium = m::mock('Solarium\Client');
        $this->fieldProvider = m::mock(TermsFieldProviderInterface::class);
        $this->terms = new SolrPrefixTermsService($this->solarium, null, $this->fieldProvider);
    }

    public function testIsTermsService()
    {
        $this->assertInstanceOf(TermsServiceInterface::class, $this->terms);
    }
}
