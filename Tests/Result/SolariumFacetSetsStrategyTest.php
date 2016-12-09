<?php

namespace Markup\NeedleBundle\Tests\Result;

use Markup\NeedleBundle\Result\SolariumFacetSetsStrategy;

/**
* A test for a strategy for fetching facet sets from a Solarium result.
*/
class SolariumFacetSetsStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->solariumResult = $this->getMockBuilder('Solarium\QueryType\Select\Result\Result')
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchContext = $this->createMock('Markup\NeedleBundle\Context\SearchContextInterface');
        $this->strategy = new SolariumFacetSetsStrategy($this->solariumResult, $this->searchContext);
    }

    public function testIsFacetSetsStrategy()
    {
        $this->assertTrue($this->strategy instanceof \Markup\NeedleBundle\Result\FacetSetStrategyInterface);
    }

    public function testGetOneFacetSet()
    {
        $facet = $this->createMock('Markup\NeedleBundle\Attribute\AttributeInterface');
        $this->searchContext
            ->expects($this->any())
            ->method('getFacets')
            ->will($this->returnValue([$facet]));
        $collatorProvider = new \Markup\NeedleBundle\Collator\NullCollatorProvider();
        $this->searchContext
            ->expects($this->any())
            ->method('getFacetCollatorProvider')
            ->will($this->returnValue($collatorProvider));
        $key = 'color';
        $facet
            ->expects($this->any())
            ->method('getSearchKey')
            ->will($this->returnValue($key));
        $solariumFacetSet = $this->getMockBuilder('Solarium\QueryType\Select\Result\FacetSet')
            ->disableOriginalConstructor()
            ->getMock();
        $solariumFacetValue = $this->createMock('Markup\NeedleBundle\Facet\FacetValueInterface');
        $this->solariumResult
            ->expects($this->any())
            ->method('getFacetSet')
            ->will($this->returnValue($solariumFacetSet));
        $solariumFacet = $this->getMockBuilder('Solarium\QueryType\Select\Result\Facet\Field')
            ->disableOriginalConstructor()
            ->getMock();
        $solariumFacetValues = new \ArrayIterator([$key => $solariumFacetValue]);
        $solariumFacet
            ->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue($solariumFacetValues));
        $solariumFacetSet
            ->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([$key => $solariumFacet])));
        $facetSets = $this->strategy->getFacetSets();
        $this->assertContainsOnly('Markup\NeedleBundle\Facet\FacetSetInterface', $facetSets);
    }

    public function testGetOneFacetSetWithResultPassedToConstructorAsClosure()
    {
        $facet = $this->createMock('Markup\NeedleBundle\Attribute\AttributeInterface');
        $this->searchContext
            ->expects($this->any())
            ->method('getFacets')
            ->will($this->returnValue([$facet]));
        $key = 'color';
        $facet
            ->expects($this->any())
            ->method('getSearchKey')
            ->will($this->returnValue($key));
        $solariumFacetSet = $this->getMockBuilder('Solarium\QueryType\Select\Result\FacetSet')
            ->disableOriginalConstructor()
            ->getMock();
        $solariumFacetValue = $this->createMock('Markup\NeedleBundle\Facet\FacetValueInterface');
        $this->solariumResult
            ->expects($this->any())
            ->method('getFacetSet')
            ->will($this->returnValue($solariumFacetSet));
        $solariumFacet = $this->getMockBuilder('Solarium\QueryType\Select\Result\Facet\Field')
            ->disableOriginalConstructor()
            ->getMock();
        $solariumFacetValues = new \ArrayIterator([$key => $solariumFacetValue]);
        $solariumFacet
            ->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue($solariumFacetValues));
        $solariumFacetSet
            ->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([$key => $solariumFacet])));
        $strategy = new SolariumFacetSetsStrategy(function() { return $this->solariumResult; }, $this->searchContext);
        $facetSets = $strategy->getFacetSets();
        $this->assertContainsOnly('Markup\NeedleBundle\Facet\FacetSetInterface', $facetSets);
    }
}
