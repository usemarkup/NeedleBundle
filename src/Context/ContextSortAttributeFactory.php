<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Context;

use Markup\NeedleBundle\Attribute\AttributeProviderInterface;
use Markup\NeedleBundle\Attribute\SpecializationContextHashInterface;
use Markup\NeedleBundle\Config\ContextConfigurationInterface;
use Markup\NeedleBundle\Sort\RelevanceSort;
use Markup\NeedleBundle\Sort\Sort;
use Markup\NeedleBundle\Sort\SortInterface;

class ContextSortAttributeFactory
{
    /**
     * @var AttributeProviderInterface
     */
    private $attributeProvider;

    public function __construct(AttributeProviderInterface $attributeProvider)
    {
        $this->attributeProvider = $attributeProvider;
    }

    public function create(
        SpecializationContextHashInterface $contextHash,
        string $attribute,
        string $direction
    ): ?SortInterface {
        if ($attribute === ContextConfigurationInterface::SORT_RELEVANCE) {
            return new RelevanceSort();
        }

        $isDescending = $direction === ContextConfigurationInterface::ORDER_DESC;

        $attribute = $this->attributeProvider->getAttributeByName($attribute, $contextHash);

        return new Sort($attribute, $isDescending);
    }
}
