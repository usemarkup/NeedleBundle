<?php

namespace Markup\NeedleBundle\Tests\Facet;

/**
* A test for a facet set iterator interface.
*/
class FacetSetIteratorInterfaceTest extends \PHPUnit_Framework_TestCase
{
    public function testIsIterator()
    {
        $refl = new \ReflectionClass('Markup\NeedleBundle\Facet\FacetSetIteratorInterface');
        $this->assertTrue($refl->implementsInterface('Iterator'));
    }

    public function testIsCountable()
    {
        $refl = new \ReflectionClass('Markup\NeedleBundle\Facet\FacetSetIteratorInterface');
        $this->assertTrue($refl->implementsInterface('Countable'));
    }
}
