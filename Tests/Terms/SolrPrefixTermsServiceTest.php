<?php

namespace Markup\NeedleBundle\Tests\Terms;

use Markup\NeedleBundle\Terms\SolrPrefixTermsService;
use Markup\NeedleBundle\Terms\TermsFieldProviderInterface;
use Mockery as m;

class SolrPrefixTermsServiceTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->solarium = m::mock('Solarium\Client');
        $this->fieldProvider = m::mock(TermsFieldProviderInterface::class);
        $this->terms = new SolrPrefixTermsService($this->solarium, null, $this->fieldProvider);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testIsTermsService()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Terms\TermsServiceInterface', $this->terms);
    }
}
