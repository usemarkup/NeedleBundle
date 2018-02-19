<?php

namespace Markup\NeedleBundle\Tests\Facet;

use Markup\NeedleBundle\Facet\FacetSetIteratorInterface;
use PHPUnit\Framework\TestCase;

/**
* A test for a facet set iterator interface.
*/
class FacetSetIteratorInterfaceTest extends TestCase
{
    public function testIsIterator()
    {
        $refl = new \ReflectionClass(FacetSetIteratorInterface::class);
        $this->assertTrue($refl->implementsInterface(\Iterator::class));
    }

    public function testIsCountable()
    {
        $refl = new \ReflectionClass(FacetSetIteratorInterface::class);
        $this->assertTrue($refl->implementsInterface(\Countable::class));
    }
}
