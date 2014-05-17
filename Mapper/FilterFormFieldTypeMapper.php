<?php

namespace Markup\NeedleBundle\Mapper;

use Markup\NeedleBundle\Attribute\AttributeProviderInterface;
use Markup\NeedleBundle\Filter\BooleanFilterInterface;
use Markup\NeedleBundle\Filter\FloatFilterInterface;

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
        if ($filter instanceof BooleanFilterInterface) {
            return 'radio';
        }

        if ($filter instanceof FloatFilterInterface) {
            return 'range_slider';
        }

        return 'select';
    }
}
