<?php

namespace Markup\NeedleBundle\Tests\Boost;

use Markup\NeedleBundle\Attribute\Attribute;
use Markup\NeedleBundle\Boost\BoostQueryField;

/**
* A test for a boost query field object.
*/
class BoostQueryFieldTest extends \PHPUnit_Framework_TestCase
{
    public function testIsBoostQueryField()
    {
        $field = new \ReflectionClass('Markup\NeedleBundle\Boost\BoostQueryField');
        $this->assertTrue($field->implementsInterface('Markup\NeedleBundle\Boost\BoostQueryFieldInterface'));
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
