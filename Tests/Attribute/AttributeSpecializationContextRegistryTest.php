<?php

namespace Markup\NeedleBundle\Tests\Attribute;

use Markup\NeedleBundle\Attribute\AttributeGenericSpecializationContext;
use Markup\NeedleBundle\Attribute\AttributeSpecialization;
use Markup\NeedleBundle\Attribute\AttributeSpecializationContextProviderInterface;
use Markup\NeedleBundle\Attribute\AttributeSpecializationContextRegistry;
use Markup\NeedleBundle\Attribute\AttributeSpecializationCurrentlyApplicableContextProviderInterface;
use Markup\NeedleBundle\Exception\UnrecognizedSpecializationException;
use Mockery as m;

/**
 * Test for filter implementation
 */
class AttributeSpecializationContextRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AttributeSpecializationContextRegistry
     */
    private $attributeSpecializationContextRegistry;

    public function setUp()
    {
        $localeSpecializationProvider = m::mock(AttributeSpecializationContextProviderInterface::class);
        $localeSpecialization =  new AttributeSpecialization('locale');
        $localeSpecializationProvider->shouldReceive('getSpecialization')->andReturn($localeSpecialization);
        $localeSpecializationProvider->shouldReceive('getContexts')->andReturn(['en_GB', 'es_ES', 'jp_JP']);

        $localeContext = new AttributeGenericSpecializationContext('es_ES');
        $currentlyApplicableLocaleSpecializationProvider = m::mock(
            AttributeSpecializationCurrentlyApplicableContextProviderInterface::class
        );
        $currentlyApplicableLocaleSpecializationProvider
            ->shouldReceive('getSpecialization')
            ->andReturn($localeSpecialization);
        $currentlyApplicableLocaleSpecializationProvider
            ->shouldReceive('getContext')
            ->andReturn($localeContext);

        $attributeSpecializationContextRegistry = new AttributeSpecializationContextRegistry();
        $attributeSpecializationContextRegistry->addAttributeSpecializationContextProvider(
            $localeSpecializationProvider
        );
        $attributeSpecializationContextRegistry->addAttributeSpecializationCurrentlyApplicableContextProvider(
            $currentlyApplicableLocaleSpecializationProvider
        );

        $this->attributeSpecializationContextRegistry = $attributeSpecializationContextRegistry;
    }

    public function tearDown()
    {
        m::close();
    }

    public function testGetSpecializationContextHashWithOneSpecialization()
    {
        $this->assertSame(
            ['locale' => 'es_ES'],
            $this->attributeSpecializationContextRegistry->getSpecializationContextHash()
        );
    }

    public function testGetSpecializationContextHashWithMissingCurrentlyApplicableContextProviderThrowsException()
    {
        // add one more provider to the collection
        $priceIdentitySpecializationProvider = m::mock(AttributeSpecializationContextProviderInterface::class);
        $priceSpecialization =  new AttributeSpecialization('price_identity');
        $priceIdentitySpecializationProvider->shouldReceive('getSpecialization')->andReturn($priceSpecialization);
        $priceIdentitySpecializationProvider->shouldReceive('getContexts')->andReturn(['gb_customer', 'jp_customer']);
        $this->attributeSpecializationContextRegistry->addAttributeSpecializationContextProvider(
            $priceIdentitySpecializationProvider
        );

        $this->setExpectedException(UnrecognizedSpecializationException::class);

        $this->attributeSpecializationContextRegistry->getSpecializationContextHash();
    }
}
