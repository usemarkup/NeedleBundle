<?php

namespace Markup\NeedleBundle\Tests\Intercept;

use Markup\NeedleBundle\Intercept\SearchInterceptMapper;

/**
* A test for a search intercept mapper that can collect mappers set on it against corpora and delegate through.
*/
class SearchInterceptMapperTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->mapper = new SearchInterceptMapper();
    }

    public function testIsInterceptMapper()
    {
        $this->assertInstanceOf('Markup\NeedleBundle\Intercept\TypedInterceptMapperInterface', $this->mapper);
    }

    public function testIsSearchType()
    {
        $this->assertEquals('search', $this->mapper->getType());
    }

    public function testNoMappersThrowsUnresolvedException()
    {
        $this->setExpectedException('Markup\NeedleBundle\Intercept\UnresolvedInterceptException');
        $definition = $this->getMock('Markup\NeedleBundle\Intercept\DefinitionInterface');
        $properties = array('corpus' => 'corpus');
        $definition
            ->expects($this->any())
            ->method('getProperties')
            ->will($this->returnValue($properties));
        $this->mapper->mapDefinitionToIntercept($definition);
    }

    public function testAddMapperMapsCorpus()
    {
        $corpus = 'corpus';
        $definition = $this->getMock('Markup\NeedleBundle\Intercept\DefinitionInterface');
        $properties = array('corpus' => $corpus);
        $definition
            ->expects($this->any())
            ->method('getProperties')
            ->will($this->returnValue($properties));
        $searchMapper = $this->getMock('Markup\NeedleBundle\Intercept\TypedInterceptMapperInterface');
        $searchMapper
            ->expects($this->any())
            ->method('getType')
            ->will($this->returnValue('search'));
        $intercept = $this->getMock('Markup\NeedleBundle\Intercept\InterceptInterface');
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
        $mapper = $this->getMock('Markup\NeedleBundle\Intercept\TypedInterceptMapperInterface');
        $mapper
            ->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($type));
        $this->setExpectedException('InvalidArgumentException');
        $this->mapper->addSearchInterceptMapper('corpus', $mapper);
    }
}
