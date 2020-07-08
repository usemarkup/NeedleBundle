<?php

namespace Markup\NeedleBundle\Tests\Attribute;

use Markup\NeedleBundle\Attribute\AttributeSpecialization;
use Markup\NeedleBundle\Attribute\AttributeSpecializationContextProviderInterface;
use Markup\NeedleBundle\Attribute\AttributeSpecializationContextRegistry;
use Markup\NeedleBundle\Attribute\SpecializationContextHash;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Test for filter implementation
 */
class AttributeSpecializationContextRegistryTest extends MockeryTestCase
{
    /**
     * @var AttributeSpecializationContextRegistry
     */
    private $attributeSpecializationContextRegistry;

    protected function setUp()
    {
        $localeSpecializationProvider = m::mock(AttributeSpecializationContextProviderInterface::class);
        $localeSpecialization =  new AttributeSpecialization('locale');
        $localeSpecializationProvider->shouldReceive('getSpecialization')->andReturn($localeSpecialization);
        $localeSpecializationProvider->shouldReceive('getContexts')->andReturn(['en_GB', 'es_ES', 'jp_JP']);

        $attributeSpecializationContextRegistry = new AttributeSpecializationContextRegistry();
        $attributeSpecializationContextRegistry->addAttributeSpecializationContextProvider(
            $localeSpecializationProvider
        );

        $this->attributeSpecializationContextRegistry = $attributeSpecializationContextRegistry;
    }

    public function testGetSpecializationContextHashWithOneSpecialization()
    {
        $this->assertSame(
            ['locale' => 'es_ES'],
            $this->attributeSpecializationContextRegistry->getSpecializationContextHashAsScalarArray(
                new SpecializationContextHash(
                    [
                        'locale' => 'es_ES',
                        'market' => 'es',
                    ]
                )
            )
        );
    }
}
