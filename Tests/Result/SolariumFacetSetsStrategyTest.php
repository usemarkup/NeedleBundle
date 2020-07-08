<?php

namespace Markup\NeedleBundle\Tests\Result;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Collator\NullCollatorProvider;
use Markup\NeedleBundle\Facet\FacetSetDecoratorProviderInterface;
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
    /**
     * @var SolariumFacetSetsStrategy
     */
    private $strategy;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|Result
     */
    private $solariumResult;

    /**
     * @var NullCollatorProvider
     */
    private $collatorProvider;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|FacetSetDecoratorProviderInterface
     */
    private $facetSetDecoratorProvider;

    public function setUp()
    {
        $this->solariumResult = $this->createMock(Result::class);
        $this->collatorProvider = new NullCollatorProvider();
        $this->facetSetDecoratorProvider = $this->createMock(FacetSetDecoratorProviderInterface::class);

        $facet = $this->createMock(AttributeInterface::class);
        $facet
            ->expects($this->any())
            ->method('getSearchKey')
            ->will($this->returnValue('color'));

        $this->strategy = new SolariumFacetSetsStrategy(
            $this->solariumResult,
            [$facet],
            $this->collatorProvider,
            $this->facetSetDecoratorProvider
        );
    }

    public function testIsFacetSetsStrategy()
    {
        $this->assertInstanceOf(FacetSetStrategyInterface::class, $this->strategy);
    }

    public function testGetOneFacetSet()
    {
        $solariumFacetSet = $this->createMock(FacetSet::class);
        $solariumFacetValue = $this->createMock(FacetValueInterface::class);
        $this->solariumResult
            ->expects($this->any())
            ->method('getFacetSet')
            ->will($this->returnValue($solariumFacetSet));
        $solariumFacet = $this->createMock(Field::class);
        $solariumFacetValues = new \ArrayIterator(['color' => $solariumFacetValue]);
        $solariumFacet
            ->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue($solariumFacetValues));
        $solariumFacetSet
            ->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator(['color' => $solariumFacet])));
        $facetSets = $this->strategy->getFacetSets();
        $this->assertContainsOnly(FacetSetInterface::class, $facetSets);
    }
}
