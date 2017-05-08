<?php

namespace Markup\NeedleBundle\Tests\Attribute;

use Markup\NeedleBundle\Attribute\AttributeSpecialization;
use Markup\NeedleBundle\Attribute\AttributeSpecializationGroup;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class AttributeSpecializationGroupTest extends MockeryTestCase
{
    public function testFormsKeyCorrectly()
    {
        $locale = new AttributeSpecialization('locale');
        $market = new AttributeSpecialization('market');

        $group = new AttributeSpecializationGroup([$locale, $market]);
        $key = json_encode(['locale', 'market']);

        $this->assertEquals($key, $group->getKey());
    }
}
