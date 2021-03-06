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

    /**
     * If the value should be interpreted as a range filter or a collection of range filters then one is returned.
     *
     * @param AttributeInterface $attribute
     * @param mixed $filterValue
     * @return SearchFilter\FilterValueInterface|null
     */
    private function getRangeFilterValue(
        AttributeInterface $attribute,
        $filterValue
    ): ?SearchFilter\FilterValueInterface {
        if ($attribute instanceof RangeFacetField) {
            if (!is_array($filterValue) || empty($filterValue)) {
                throw new \RuntimeException('Cannot provide range filter value given the passed filter values');
            }

            if (isset($filterValue['min'], $filterValue['max'])) {
                return new SearchFilter\RangeFilterValue(
                    floatval($filterValue['min']),
                    floatval($filterValue['max'])
                );
            }

            $ranges = [];
            foreach ($filterValue as $min) {
                $min = floatval($min);
                $max = $min + $attribute->getRangeSize() - 0.01;
                $ranges[] = new SearchFilter\RangeFilterValue($min, $max);
            }
            if (count($ranges) === 1) {
                return $ranges[0];
            }
            return new SearchFilter\UnionFilterValue($ranges);
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
