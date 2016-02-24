<?php

namespace Markup\NeedleBundle\Tests\Terms;

use Markup\NeedleBundle\Terms\SolrRegexTermsService;
use Markup\NeedleBundle\Terms\TermsFieldProviderInterface;
use Mockery as m;

class SolrRegexTermsServiceTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->solarium = m::mock('Solarium\Client');
        $this->fieldProvider = m::mock(TermsFieldProviderInterface::class);
        $this->terms = new SolrRegexTermsService($this->solarium, null, $this->fieldProvider);
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
