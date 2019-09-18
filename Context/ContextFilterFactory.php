<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Context;

use Markup\NeedleBundle\Attribute\AttributeProviderInterface;
use Markup\NeedleBundle\Attribute\FloatAttributeInterface;
use Markup\NeedleBundle\Attribute\SpecializationContextHashInterface;
use Markup\NeedleBundle\Filter as SearchFilter;
use Markup\NeedleBundle\Filter\FilterQueryInterface;

final class ContextFilterFactory
{
    /**
     * @var SpecializationContextHashInterface
     */
    private $contextHash;

    /**
     * @var AttributeProviderInterface
     */
    private $attributeProvider;

    public function __construct(
        AttributeProviderInterface $attributeProvider,
        SpecializationContextHashInterface $contextHash
    ) {
        $this->contextHash = $contextHash;
        $this->attributeProvider = $attributeProvider;
    }

    /**
     * @param string $filter
     * @param int|bool|string|array $filterValue
     * @return FilterQueryInterface
     */
    public function create(string $filter, $filterValue): ?FilterQueryInterface
    {
        $attribute = $this->attributeProvider->getAttributeByName($filter, $this->contextHash);

        if (!$attribute) {
            return null;
        }

        if ($attribute instanceof FloatAttributeInterface) {
            if (is_array($filterValue)) {
                if (!isset($filterValue['min'], $filterValue['max'])) {
                    return null;
                }

                if (!is_numeric($filterValue['min']) && !is_numeric($filterValue['max'])) {
                    return null;
                }

                if (!is_numeric($filterValue['max'])) {
                    $filterValue['max'] = 1000000000;
                }

                if (!is_numeric($filterValue['min'])) {
                    $filterValue['min'] = 0;
                }

                $filterValue = new SearchFilter\RangeFilterValue(
                    floatval($filterValue['min']),
                    floatval($filterValue['max'])
                );
            }
        }

        if (is_array($filterValue)) {
            $filterValue = new SearchFilter\UnionFilterValue(
                array_map(
                    function ($value) {
                        return new SearchFilter\ScalarFilterValue($value);
                    },
                    $filterValue
                )
            );
        } elseif (!$filterValue instanceof SearchFilter\RangeFilterValue) {
            $filterValue = new SearchFilter\ScalarFilterValue($filterValue);
        }

        return new SearchFilter\FilterQuery($attribute, $filterValue);
    }
}
