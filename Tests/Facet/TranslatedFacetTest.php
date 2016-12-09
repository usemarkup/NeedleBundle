<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Facet\TranslatedFacet;

/**
* A test for a facet that has a display name provided by a translation using the facet's name.
*/
class TranslatedFacetTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
    }

    public function testIsAttribute()
    {
        $translatedFacet = new \ReflectionClass('Markup\NeedleBundle\Facet\TranslatedFacet');
        $this->assertTrue($translatedFacet->implementsInterface('Markup\NeedleBundle\Attribute\AttributeInterface'));
    }

    public function testGetName()
    {
        $name = 'color';
        $facet = new TranslatedFacet($name, $this->translator, 'subdomain');
        $this->assertEquals($name, $facet->getName());
    }

    public function testGetDisplayName()
    {
        $name = 'color';
        $translationNamespace = 'catalog.facet';
        $displayName = 'Colour';
        $this->translator
            ->expects($this->any())
            ->method('trans')
            ->with($this->equalTo('catalog.facet.color'))
            ->will($this->returnValue($displayName));
        $facet = new TranslatedFacet($name, $this->translator, $translationNamespace);
        $this->assertEquals($displayName, $facet->getDisplayName());
    }

    public function testGetDisplayNameUsingMessageDomain()
    {
        $name = 'color';
        $translationNamespace = 'catalog.facet';
        $messageDomain = 'domain';
        $displayName = 'Colour';
        $this->translator
            ->expects($this->any())
            ->method('trans')
            ->with($this->equalTo('catalog.facet.color'), $this->equalTo([]), $this->equalTo($messageDomain))
            ->will($this->returnValue($displayName));
        $facet = new TranslatedFacet($name, $this->translator, $translationNamespace, $messageDomain);
        $this->assertEquals($displayName, $facet->getDisplayName());
    }

    public function testGetDisplayNameUsingEmptyNamespace()
    {
        $name = 'facet.color';
        $translationNamespace = '';
        $displayName = 'Colour';
        $this->translator
            ->expects($this->any())
            ->method('trans')
            ->with($this->equalTo('facet.color'))
            ->will($this->returnValue($displayName));
        $facet = new TranslatedFacet($name, $this->translator, $translationNamespace);
        $this->assertEquals($displayName, $facet->getDisplayName());
    }

    public function testGetSearchKeyWhenNotExplicitlySet()
    {
        $name = 'color';
        $facet = new TranslatedFacet($name, $this->translator, 'subdomain');
        $this->assertEquals($name, $facet->getSearchKey());
    }

    public function testGetSearchKeyWhenExplicitlySet()
    {
        $name = 'color';
        $searchKey = 'color_group';
        $facet = new TranslatedFacet($name, $this->translator, 'subdomain', null, $searchKey);
        $this->assertEquals($searchKey, $facet->getSearchKey());
    }

    public function testCastToString()
    {
        $name = 'color';
        $translationNamespace = 'catalog.facet';
        $displayName = 'Colour';
        $this->translator
            ->expects($this->any())
            ->method('trans')
            ->with($this->equalTo('catalog.facet.color'))
            ->will($this->returnValue($displayName));
        $facet = new TranslatedFacet($name, $this->translator, $translationNamespace);
        $this->assertEquals($displayName, (string) $facet);
    }
}
