<?php

namespace Markup\NeedleBundle\Tests\Attribute;

use Markup\NeedleBundle\Attribute\AttributeGenericSpecializationContext;
use Markup\NeedleBundle\Attribute\AttributeSpecialization;
use Markup\NeedleBundle\Attribute\AttributeSpecializationContextGroupRegistry;
use Markup\NeedleBundle\Attribute\AttributeSpecializationContextRegistryInterface;
use Markup\NeedleBundle\Attribute\AttributeSpecializationProvider;
use Markup\NeedleBundle\Attribute\CompositeSpecializationContextGroupFilter;
use Markup\NeedleBundle\Attribute\SpecializationContextGroupFilterInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class AttributeSpecializationContextGroupRegistryTest extends MockeryTestCase
{
    /**
     * @var AttributeSpecializationContextRegistryInterface|m\MockInterface
     */
    private $attributeSpecializationContextRegistry;

    /**
     * @var CompositeSpecializationContextGroupFilter
     */
    private $contextFilter;

    /**
     * @var AttributeSpecializationContextGroupRegistry
     */
    private $attributeSpecializationContextGroupRegistry;

    /**
     * @var AttributeSpecializationProvider|m\LegacyMockInterface|m\MockInterface
     */
    private $attributeSpecializationProvider;

    protected function setUp()
    {
        $this->attributeSpecializationContextRegistry = m::mock(AttributeSpecializationContextRegistryInterface::class);
        $this->attributeSpecializationProvider = m::mock(AttributeSpecializationProvider::class);
        $this->attributeSpecializationContextRegistry
            ->shouldReceive('getContexts')
            ->with('locale')
            ->andReturn([
                new AttributeGenericSpecializationContext('en_GB'),
                new AttributeGenericSpecializationContext('de_DE'),
                new AttributeGenericSpecializationContext('es_ES'),
                new AttributeGenericSpecializationContext('jp_JP')
            ]);

        $this->attributeSpecializationContextRegistry
            ->shouldReceive('getContexts')
            ->with('price_identity')
            ->andReturn([
                new AttributeGenericSpecializationContext('jp_customer'),
                new AttributeGenericSpecializationContext('uk_customer'),
                new AttributeGenericSpecializationContext('euro_customer'),
            ]);

        $this->attributeSpecializationContextRegistry
            ->shouldReceive('getContexts')
            ->with('stock_location')
            ->andReturn([
                new AttributeGenericSpecializationContext('eastern_warehouse'),
                new AttributeGenericSpecializationContext('euro_warehouse')
            ]);

        $this->contextFilter = new CompositeSpecializationContextGroupFilter();

        $this->attributeSpecializationContextGroupRegistry = new AttributeSpecializationContextGroupRegistry(
            $this->attributeSpecializationContextRegistry,
            $this->contextFilter,
            $this->attributeSpecializationProvider
        );
    }

    public function testGetAllAttributeSpecializationGroupsWithOneSpecialization()
    {
        $contextGroups = $this->attributeSpecializationContextGroupRegistry->getAllAttributeSpecializationContextGroups(
            [$this->getSpecialization('locale')]
        );

        $this->assertEquals(4, count($contextGroups));
    }

    public function testGetAllAttributeSpecializationGroupsWithMultipleSpecializations()
    {
        $contextGroups = $this->attributeSpecializationContextGroupRegistry->getAllAttributeSpecializationContextGroups(
            [$this->getSpecialization('locale'), $this->getSpecialization('price_identity')]
        );

        $this->assertEquals(12, count($contextGroups));
    }

    public function testGetAllValidAttributeSpecializationGroups()
    {
        $dataToReject = [
            'locale' => 'de_DE',
            'price_identity' => 'uk_customer',
        ];
        $filter = m::mock(SpecializationContextGroupFilterInterface::class)
            ->shouldReceive('accept')
            ->andReturnUsing(function (array $data) use ($dataToReject) {
                return $data !== $dataToReject;
            })
            ->getMock();
        $this->contextFilter->addFilter($filter);

        $contextGroups = $this->attributeSpecializationContextGroupRegistry->getAllValidAttributeSpecializationContextGroups(
            ['locale', 'price_identity']
        );

        $this->assertEquals(11, count($contextGroups));
    }

    private function getSpecialization($key)
    {
        switch($key) {
            case 'locale':
                return new AttributeSpecialization('locale');
            case 'price_identity':
                return new AttributeSpecialization('price_identity');
            case 'stock_location':
                return new AttributeSpecialization('stock_location');
        }
    }
}
