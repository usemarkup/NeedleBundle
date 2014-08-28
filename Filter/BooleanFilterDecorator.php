<?php

namespace Markup\NeedleBundle\Filter;

use Markup\NeedleBundle\Attribute\BooleanAttributeInterface;

/**
* A decorator for a filter that declares a Boolean type (clocking any underlying type).
*/
class BooleanFilterDecorator extends FilterDecorator implements BooleanAttributeInterface
{}
