<?php

namespace Markup\NeedleBundle\Tests\Intercept;

use Markup\NeedleBundle\Intercept\DefinitionInterface;
use Markup\NeedleBundle\Intercept\InterceptInterface;
use Markup\NeedleBundle\Intercept\SearchInterceptMapper;
use Markup\NeedleBundle\Intercept\TypedInterceptMapperInterface;
use Markup\NeedleBundle\Intercept\UnresolvedInterceptException;
use PHPUnit\Framework\TestCase;

/**
* A test for a search intercept mapper that can collect mappers set on it against corpora and delegate through.
*/
class SearchInterceptMapperTest extends TestCase
{
    /**
     * @var SearchInterceptMapper
     */
    private $mapper;

    protected function setUp()
    {
        $this->mapper = new SearchInterceptMapper();
    }

    public function testIsInterceptMapper()
    {
        $this->assertInstanceOf(TypedInterceptMapperInterface::class, $this->mapper);
    }

    public function testIsSearchType()
    {
        $this->assertEquals('search', $this->mapper->getType());
    }

    public function testNoMappersThrowsUnresolvedException()
    {
        $this->expectException(UnresolvedInterceptException::class);
        $definition = $this->createMock(DefinitionInterface::class);
        $properties = ['corpus' => 'corpus'];
        $definition
            ->expects($this->any())
            ->method('getProperties')
            ->will($this->returnValue($properties));
        $this->mapper->mapDefinitionToIntercept($definition);
    }

    public function testAddMapperMapsCorpus()
    {
        $corpus = 'corpus';
        $definition = $this->createMock(DefinitionInterface::class);
        $properties = ['corpus' => $corpus];
        $definition
            ->expects($this->any())
            ->method('getProperties')
            ->will($this->returnValue($properties));
        $searchMapper = $this->createMock(TypedInterceptMapperInterface::class);
        $searchMapper
            ->expects($this->any())
            ->method('getType')
            ->will($this->returnValue('search'));
        $intercept = $this->createMock(InterceptInterface::class);
        $searchMapper
            ->expects($this->any())
            ->method('mapDefinitionToIntercept')
            ->will($this->returnValue($intercept));
        $this->mapper->addSearchInterceptMapper($corpus, $searchMapper);
        $this->assertSame($intercept, $this->mapper->mapDefinitionToIntercept($definition));
    }

    public function testAddNonSearchInterceptMapperThrowsInvalidArgumentException()
    {
        $type = 'something';
        $mapper = $this->createMock(TypedInterceptMapperInterface::class);
        $mapper
            ->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($type));
        $this->expectException('InvalidArgumentException');
        $this->mapper->addSearchInterceptMapper('corpus', $mapper);
    }
}
