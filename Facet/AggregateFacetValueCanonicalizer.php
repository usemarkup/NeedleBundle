<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Facet;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * Allows for FacetValueCanonicalizerInterface to be defined against each
 * facet and executed
 */
final class AggregateFacetValueCanonicalizer implements FacetValueCanonicalizerInterface
{
    /**
     * @var ServiceLocator
     */
    private $serviceLocator;

    public function __construct(ServiceLocator $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * {@inheritdoc}
     **/
    public function canonicalizeForFacet(string $value, AttributeInterface $facet): string
    {
        if (!$this->serviceLocator->has($facet->getName())) {
            return $value;
        }

        $canonicalizer = $this->serviceLocator->get($facet->getName());

        if (!$canonicalizer instanceof FacetValueCanonicalizerInterface) {
            throw new \LogicException('$canonicalizer is expected to be FacetValueCanonicalizerInterface');
        }

        return $canonicalizer->canonicalizeForFacet($value, $facet);
    }
}
