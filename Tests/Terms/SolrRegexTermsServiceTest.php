<?php

namespace Markup\NeedleBundle\Tests\Terms;

use Markup\NeedleBundle\Query\SimpleQueryInterface;
use Markup\NeedleBundle\Terms\EmptyTermsResult;
use Markup\NeedleBundle\Terms\SolrRegexTermsService;
use Markup\NeedleBundle\Terms\SolrTermsResult;
use Markup\NeedleBundle\Terms\TermsFieldProviderInterface;
use Mockery as m;
use Solarium\Exception\HttpException as SolariumException;
use Solarium\QueryType\Terms\Query;
use Solarium\QueryType\Terms\Result;

class SolrRegexTermsServiceTest extends \PHPUnit_Framework_TestCase
{
    private $solarium;
    private $fieldProvider;
    private $terms;

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

    public function testFetchTermsWhenNoFieldsReturned()
    {
        $query = m::mock(SimpleQueryInterface::class);

        $this->solarium->shouldReceive('createTerms')->once();
        $this->fieldProvider->shouldReceive('getField')->once();

        $result = $this->terms->fetchTerms($query);

        $this->assertInstanceOf(EmptyTermsResult::class, $result);
    }

    public function testFetchTermsReturnsTermsResult()
    {
        $field = 'field';
        $term = 'search';
        $wildcardTerm = '.*search.*';

        $query = m::mock(SimpleQueryInterface::class);
        $query->shouldReceive('getSearchTerm')->andReturn($term);

        $suggestQuery = m::mock(Query::class);
        $suggestQuery->shouldReceive('setFields')->with($field);
        $suggestQuery->shouldReceive('setRegex')->with($wildcardTerm);
        $suggestQuery->shouldReceive('setSort')->with('count');

        $termsResult = m::mock(Result::class);

        $this->solarium->shouldReceive('createTerms')->andReturn($suggestQuery);
        $this->solarium->shouldReceive('terms')->with($suggestQuery)
            ->andReturn($termsResult);
        $this->fieldProvider->shouldReceive('getField')->andReturn($field);

        $result = $this->terms->fetchTerms($query);

        $this->assertInstanceOf(SolrTermsResult::class, $result);
    }

    public function testFetchTermsReturnsTermsResultEscapesLuceneChars()
    {
        $field = 'field';
        $term = 'search[';
        $escapedTerm = '.*search\[.*';

        $query = m::mock(SimpleQueryInterface::class);
        $query->shouldReceive('getSearchTerm')->andReturn($term);

        $suggestQuery = m::mock(Query::class);
        $suggestQuery->shouldReceive('setFields')->with($field);
        $suggestQuery->shouldReceive('setRegex')->with($escapedTerm);
        $suggestQuery->shouldReceive('setSort')->with('count');

        $termsResult = m::mock(Result::class);

        $this->solarium->shouldReceive('createTerms')->andReturn($suggestQuery);
        $this->solarium->shouldReceive('terms')->with($suggestQuery)
            ->andReturn($termsResult);
        $this->fieldProvider->shouldReceive('getField')->andReturn($field);

        $result = $this->terms->fetchTerms($query);

        $this->assertInstanceOf(SolrTermsResult::class, $result);
    }

    public function testFetchTermsReturnsEmptyTermsResultWhenException()
    {
        $field = 'field';
        $term = 'search';
        $wildcardTerm = '.*search.*';

        $query = m::mock(SimpleQueryInterface::class);
        $query->shouldReceive('getSearchTerm')->andReturn($term);

        $suggestQuery = m::mock(Query::class);
        $suggestQuery->shouldReceive('setFields')->with($field);
        $suggestQuery->shouldReceive('setRegex')->with($wildcardTerm);
        $suggestQuery->shouldReceive('setSort')->with('count');

        $this->solarium->shouldReceive('createTerms')->andReturn($suggestQuery);
        $this->solarium->shouldReceive('terms')->with($suggestQuery)
            ->andThrow(SolariumException::class);
        $this->fieldProvider->shouldReceive('getField')->andReturn($field);

        $result = $this->terms->fetchTerms($query);

        $this->assertInstanceOf(EmptyTermsResult::class, $result);
    }
}
