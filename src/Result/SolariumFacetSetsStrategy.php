<?php

namespace Markup\NeedleBundle\Result;

use Markup\NeedleBundle\Collator\CollatorProviderInterface;
use Markup\NeedleBundle\Facet\FacetSetDecoratorProviderInterface;
use Markup\NeedleBundle\Facet\FacetSetInterface;
use Markup\NeedleBundle\Facet\FacetValueCanonicalizerInterface;
use Markup\NeedleBundle\Facet\NoopFacetValueCanonicalizer;
use Solarium\QueryType\Select\Result\Result as SolariumResult;

/**
* A strategy for fetching facet sets from a Solarium result.
*/
class SolariumFacetSetsStrategy implements FacetSetStrategyInterface
{
    /**
     * @var SolariumResult
     **/
    private $solariumResult;

    /**
     * A closure that returns a Solarium result object.
     *
     * @var \Closure
     **/
    private $solariumResultClosure;

    /**
     * @var FacetValueCanonicalizerInterface
     */
    private $facetValueCanonicalizer;

    /**
     * @var array
     */
    private $facets;

    /**
     * @var CollatorProviderInterface
     */
    private $collatorProvider;

    /**
     * @var FacetSetDecoratorProviderInterface
     */
    private $facetSetDecoratorProvider;

    public function __construct(
        $result,
        array $facets,
        CollatorProviderInterface $collatorProvider,
        FacetSetDecoratorProviderInterface $facetSetDecoratorProvider,
        FacetValueCanonicalizerInterface $facetValueCanonicalizer = null
    ) {
        if ($result instanceof SolariumResult) {
            $this->solariumResult = $result;
        } elseif ($result instanceof \Closure) {
            $this->solariumResultClosure = $result;
        } else {
            throw new \InvalidArgumentException(sprintf('Passed an instance of %s as a result into %s. Expected a Solarium result instance (Solarium\QueryType\Select\Result\Result) or a closure that returns a Solarium result instance.', get_class($result), __METHOD__));
        }
        $this->facetValueCanonicalizer = $facetValueCanonicalizer ?: new NoopFacetValueCanonicalizer();
        $this->facets = $facets;
        $this->collatorProvider = $collatorProvider;
        $this->facetSetDecoratorProvider = $facetSetDecoratorProvider;
    }

    public function getFacetSets()
    {
        /** @var FacetSetInterface[] $facetSets */
        $facetSets = new SolariumFacetSetsIterator(
            $this->facetValueCanonicalizer,
            $this->getSolariumResult()->getFacetSet(),
            $this->facets,
            $this->collatorProvider,
            $this->facetSetDecoratorProvider
        );

        return $facetSets;
    }

    /**
     * @return SolariumResult
     **/
    private function getSolariumResult()
    {
        if (null === $this->solariumResult) {
            $this->solariumResult = $this->solariumResultClosure->__invoke();
        }

        return $this->solariumResult;
    }
}
