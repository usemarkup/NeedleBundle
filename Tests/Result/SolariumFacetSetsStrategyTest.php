<?php

namespace Markup\NeedleBundle\Tests\Result;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Context\SearchContextInterface;
use Markup\NeedleBundle\Facet\FacetSetInterface;
use Markup\NeedleBundle\Facet\FacetValueInterface;
use Markup\NeedleBundle\Result\FacetSetStrategyInterface;
use Markup\NeedleBundle\Result\SolariumFacetSetsStrategy;
use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Select\Result\Facet\Field;
use Solarium\QueryType\Select\Result\FacetSet;
use Solarium\QueryType\Select\Result\Result;

/**
* A test for a strategy for fetching facet sets from a Solarium result.
*/
class SolariumFacetSetsStrategyTest extends TestCase
{
    public function setUp()
    {
        $this->solariumResult = $this->createMock(Result::class);
        $this->searchContext = $this->createMock(SearchContextInterface::class);
        $this->strategy = new SolariumFacetSetsStrategy($this->solariumResult, $this->searchContext);
    }

    public function testIsFacetSetsStrategy()
    {
        $this->assertInstanceOf(FacetSetStrategyInterface::class, $this->strategy);
    }

    public function testGetOneFacetSet()
    {
        $facet = $this->createMock(AttributeInterface::class);
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
        $solariumFacetSet = $this->createMock(FacetSet::class);
        $solariumFacetValue = $this->createMock(FacetValueInterface::class);
        $this->solariumResult
            ->expects($this->any())
            ->method('getFacetSet')
            ->will($this->returnValue($solariumFacetSet));
        $solariumFacet = $this->createMock(Field::class);
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
        $this->assertContainsOnly(FacetSetInterface::class, $facetSets);
    }

    public function testGetOneFacetSetWithResultPassedToConstructorAsClosure()
    {
        $facet = $this->createMock(AttributeInterface::class);
        $this->searchContext
            ->expects($this->any())
            ->method('getFacets')
            ->will($this->returnValue([$facet]));
        $key = 'color';
        $facet
            ->expects($this->any())
            ->method('getSearchKey')
            ->will($this->returnValue($key));
        $solariumFacetSet = $this->createMock(FacetSet::class);
        $solariumFacetValue = $this->createMock(FacetValueInterface::class);
        $this->solariumResult
            ->expects($this->any())
            ->method('getFacetSet')
            ->will($this->returnValue($solariumFacetSet));
        $solariumFacet = $this->createMock(Field::class);
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
        $this->assertContainsOnly(FacetSetInterface::class, $facetSets);
    }
}
