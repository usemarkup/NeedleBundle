<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Context;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Attribute\AttributeProviderInterface;
use Markup\NeedleBundle\Attribute\FloatAttributeInterface;
use Markup\NeedleBundle\Attribute\SpecializationContextHashInterface;
use Markup\NeedleBundle\Facet\RangeFacetField;
use Markup\NeedleBundle\Filter as SearchFilter;
use Markup\NeedleBundle\Filter\FilterQueryInterface;

final class ContextFilterFactory
{
    /**
     * @var AttributeProviderInterface
     */
    private $attributeProvider;

    public function __construct(
        AttributeProviderInterface $attributeProvider
    ) {
        $this->attributeProvider = $attributeProvider;
    }

    /**
     * @param string $filter
     * @param int|bool|string|array|SearchFilter\RangeFilterValue $filterValue
     * @param SpecializationContextHashInterface $contextHash
     * @return FilterQueryInterface
     */
    public function create(
        string $filter,
        $filterValue,
        SpecializationContextHashInterface $contextHash
    ): ?FilterQueryInterface {
        $attribute = $this->attributeProvider->getAttributeByName($filter, $contextHash);

        if ($filterValue instanceof SearchFilter\RangeFilterValue) {
            return new SearchFilter\FilterQuery($attribute, $filterValue);
        }

        $rangeFilterValue = $this->getRangeFilterValue($attribute, $filterValue);
        if ($rangeFilterValue) {
            return new SearchFilter\FilterQuery($attribute, $rangeFilterValue);
        }

        if (is_array($filterValue)) {
            // by default at the moment all filterValues seem to come in as an array due to the way
            // we form requests. this results in union filter values when scalar filter values would also work
            // this shouldn't be an issue but this comment serves as a notice that the fallback return statement
            // of this method will not often be used during runtime
            $filterValue = new SearchFilter\UnionFilterValue(
                array_map(
                    function ($value) {
                        return new SearchFilter\ScalarFilterValue($value);
                    },
                    $filterValue
                )
            );

            return new SearchFilter\FilterQuery($attribute, $filterValue);
        }

        return new SearchFilter\FilterQuery($attribute, new SearchFilter\ScalarFilterValue($filterValue));
    }

    // if the value can/should be interpreted as a range filter value then one is returned
    private function getRangeFilterValue(AttributeInterface $attribute, $filterValue): ?SearchFilter\RangeFilterValue
    {
        if ($attribute instanceof RangeFacetField) {
            if (is_array($filterValue) && isset($filterValue['min'], $filterValue['max'])) {
                return new SearchFilter\RangeFilterValue(
                    floatval($filterValue['min']),
                    floatval($filterValue['max'])
                );
            }

            if (is_array($filterValue)) {
                $min = floatval(min($filterValue));
                $max = floatval(max($filterValue)) + (floatval($attribute->getRangeSize()) - 0.01);

                return new SearchFilter\RangeFilterValue($min, $max);
            }

            throw new \RuntimeException('Cannot provide range filter value given the passed filter values');
        }

        if (!$attribute instanceof FloatAttributeInterface) {
            return null;
        }

        if (!is_array($filterValue)) {
            return null;
        }

        if (count($filterValue) > 2) {
            return null;
        }

        if (count($filterValue) === 1 && array_key_exists('min', $filterValue)) {
            $filterValue['max'] = 1000000000;
        }

        if (count($filterValue) === 1 && array_key_exists('max', $filterValue)) {
            $filterValue['min'] = 0;
        }

        if (!isset($filterValue['min'], $filterValue['max'])) {
            return null;
        }

        if (!is_numeric($filterValue['min']) && !is_numeric($filterValue['max'])) {
            return null;
        }

        return new SearchFilter\RangeFilterValue(
            floatval($filterValue['min']),
            floatval($filterValue['max'])
        );
    }
}
