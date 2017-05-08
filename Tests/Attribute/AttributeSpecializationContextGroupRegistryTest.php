<?php

namespace Markup\NeedleBundle\Tests\Attribute;

use Markup\NeedleBundle\Attribute\AttributeGenericSpecializationContext;
use Markup\NeedleBundle\Attribute\AttributeSpecialization;
use Markup\NeedleBundle\Attribute\AttributeSpecializationContextGroupRegistry;
use Markup\NeedleBundle\Attribute\AttributeSpecializationInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Markup\NeedleBundle\Attribute\AttributeSpecializationContextRegistryInterface;

class AttributeSpecializationContextGroupRegistryTest extends MockeryTestCase
{
    /**
     * @var AttributeSpecializationContextRegistryInterface|m\Mock
     */
    private $attributeSpecializationContextRegistry;

    /**
     * @var AttributeSpecializationContextGroupRegistry
     */
    private $attributeSpecializationContextGroupRegistry;

    public function setUp()
    {
        $this->attributeSpecializationContextRegistry = m::mock(AttributeSpecializationContextRegistryInterface::class);
        $this->attributeSpecializationContextRegistry
            ->shouldReceive('getContexts')
            ->with(m::on(function(AttributeSpecializationInterface $specialization) {
                return $specialization->getName() === 'locale';
            }))
            ->andReturn([
                new AttributeGenericSpecializationContext('en_GB'),
                new AttributeGenericSpecializationContext('de_DE'),
                new AttributeGenericSpecializationContext('es_ES'),
                new AttributeGenericSpecializationContext('jp_JP')
            ]);
        $this->attributeSpecializationContextRegistry
            ->shouldReceive('getContext')
            ->with(m::on(function(AttributeSpecializationInterface $specialization) {
                return $specialization->getName() === 'locale';
            }))
            ->andReturn(
                new AttributeGenericSpecializationContext('de_DE')
            );

        $this->attributeSpecializationContextRegistry
            ->shouldReceive('getContexts')
            ->with(m::on(function(AttributeSpecializationInterface $specialization) {
                return $specialization->getName() === 'price_identity';
            }))
            ->andReturn([
                new AttributeGenericSpecializationContext('jp_customer'),
                new AttributeGenericSpecializationContext('uk_customer'),
                new AttributeGenericSpecializationContext('euro_customer'),
            ]);
        $this->attributeSpecializationContextRegistry
            ->shouldReceive('getContext')
            ->with(m::on(function(AttributeSpecializationInterface $specialization) {
                return $specialization->getName() === 'price_identity';
            }))
            ->andReturn(
                new AttributeGenericSpecializationContext('euro_customer')
            );

        $this->attributeSpecializationContextRegistry
            ->shouldReceive('getContexts')
            ->with(m::on(function(AttributeSpecializationInterface $specialization) {
                return $specialization->getName() === 'stock_location';
            }))
            ->andReturn([
                new AttributeGenericSpecializationContext('eastern_warehouse'),
                new AttributeGenericSpecializationContext('euro_warehouse')
            ]);

        $this->attributeSpecializationContextRegistry
            ->shouldReceive('getContext')
            ->with(m::on(function(AttributeSpecializationInterface $specialization) {
                return $specialization->getName() === 'stock_location';
            }))
            ->andReturn(
                new AttributeGenericSpecializationContext('euro_warehouse')
            );

        $this->attributeSpecializationContextGroupRegistry = new AttributeSpecializationContextGroupRegistry(
            $this->attributeSpecializationContextRegistry
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

    public function testGetCurrentAttributeSpecializationGroupWithOneSpecialization()
    {
        $contextGroups = $this->attributeSpecializationContextGroupRegistry->getCurrentAttributeSpecializationContextGroup(
            [$this->getSpecialization('locale')]
        );

        $key = json_encode(['locale' => 'de_DE']);

        $this->assertEquals($key, $contextGroups->getKey());
    }

    public function testGetCurrentAttributeSpecializationGroupWithMultipleSpecializations()
    {
        $contextGroups = $this->attributeSpecializationContextGroupRegistry->getCurrentAttributeSpecializationContextGroup(
            [$this->getSpecialization('price_identity'), $this->getSpecialization('stock_location')]
        );

        $key = json_encode(['price_identity' => 'euro_customer', 'stock_location' => 'euro_warehouse']);

        $this->assertEquals($key, $contextGroups->getKey());
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
