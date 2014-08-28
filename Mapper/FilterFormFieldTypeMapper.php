<?php

namespace Markup\NeedleBundle\Mapper;

use Markup\NeedleBundle\Attribute\AttributeProviderInterface;
use Markup\NeedleBundle\Attribute\BooleanAttributeInterface;
use Markup\NeedleBundle\Attribute\FloatAttributeInterface;

/**
* Maps class instances of Filter Query Objects to form field types that should be used to render them
*/
class FilterFormFieldTypeMapper
{
    private $filterProvider;

    public function __construct(AttributeProviderInterface $filterProvider)
    {
        $this->filterProvider = $filterProvider;
    }

    public function getTypeByFilterName($name)
    {
        $filter = $this->filterProvider->getAttributeByName($name);

        return $this->getFormFieldTypeByFilter($filter);
    }

    private function getFormFieldTypeByFilter($filter)
    {
        if ($filter instanceof BooleanAttributeInterface) {
            return 'radio';
        }

        if ($filter instanceof FloatAttributeInterface) {
            return 'range_slider';
        }

        return 'select';
    }
}
