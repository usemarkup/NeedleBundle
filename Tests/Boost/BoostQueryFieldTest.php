<?php

namespace Markup\NeedleBundle\Tests\Boost;

use Markup\NeedleBundle\Attribute\Attribute;
use Markup\NeedleBundle\Boost\BoostQueryField;
use Markup\NeedleBundle\Boost\BoostQueryFieldInterface;
use PHPUnit\Framework\TestCase;

/**
* A test for a boost query field object.
*/
class BoostQueryFieldTest extends TestCase
{
    public function testIsBoostQueryField()
    {
        $field = new \ReflectionClass(BoostQueryField::class);
        $this->assertTrue($field->implementsInterface(BoostQueryFieldInterface::class));
    }

    public function testGetAttributeName()
    {
        $attrName = 'boots';
        $attr = new Attribute($attrName);
        $field = new BoostQueryField($attr);
        $this->assertSame($attr, $field->getAttribute());
    }

    public function testGetBoost()
    {
        $boostFactor = 42;
        $field = new BoostQueryField(new Attribute('attr'), $boostFactor);
        $this->assertEquals($boostFactor, $field->getBoostFactor());
    }
}
