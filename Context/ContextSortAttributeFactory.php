<?php

namespace Markup\NeedleBundle\Context;

use Markup\NeedleBundle\Attribute\AttributeProviderInterface;
use Markup\NeedleBundle\Config\ContextConfigurationInterface;
use Markup\NeedleBundle\Sort\RelevanceSort;
use Markup\NeedleBundle\Sort\Sort;

/**
 * Class ContextSortAttributeFactory
 */
class ContextSortAttributeFactory
{
    private $attributeProvider;

    public function __construct(AttributeProviderInterface $attributeProvider)
    {
        $this->attributeProvider = $attributeProvider;
    }

    /**
     * @param string $attr
     * @param string $direction
     *
     * @return RelevanceSort|Sort
     */
    public function createSortForAttributeNameAndDirection($attr, $direction)
    {
        if ($attr === ContextConfigurationInterface::SORT_RELEVANCE) {
            return new RelevanceSort();
        }

        $isDescending = $direction === ContextConfigurationInterface::ORDER_DESC;

        return new Sort($this->attributeProvider->getAttributeByName($attr), $isDescending);
    }
}
