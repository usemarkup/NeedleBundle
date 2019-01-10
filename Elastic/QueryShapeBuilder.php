<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Elastic;

use Markup\NeedleBundle\Filter\FilterQueryInterface;
use Markup\NeedleBundle\Filter\FilterValueInterface;
use Markup\NeedleBundle\Filter\IntersectionFilterValueInterface;
use Markup\NeedleBundle\Filter\RangeFilterValueInterface;
use Markup\NeedleBundle\Filter\UnionFilterValueInterface;

/**
 * Class for an Elasticsearch helper object that can build query shapes for filter queries.
 */
class QueryShapeBuilder
{
    public function getQueryShapeForFilterQuery(FilterQueryInterface $filterQuery): ?array
    {
        return $this->getQueryShapeForSeparatedFilterQuery($filterQuery->getSearchKey(), $filterQuery->getFilterValue());
    }

    private function getQueryShapeForSeparatedFilterQuery(string $searchKey, FilterValueInterface $filterValue): ?array
    {
        switch ($filterValue->getValueType()) {
            case FilterValueInterface::TYPE_SIMPLE:
                return [
                    'term' => [
                        $searchKey => $filterValue->getSearchValue(),
                    ],
                ];
            case FilterValueInterface::TYPE_UNION:
                /** @var UnionFilterValueInterface $unionValue */
                $unionValue = $filterValue;

                return [
                    'bool' => [
                        'should' => array_map(
                            function (FilterValueInterface $filterValue) use ($searchKey) {
                                return $this->getQueryShapeForSeparatedFilterQuery($searchKey, $filterValue);
                            },
                            $unionValue->getValues()
                        ),
                    ],
                ];
            case FilterValueInterface::TYPE_INTERSECTION:
                /** @var IntersectionFilterValueInterface $intersectionValue */
                $intersectionValue = $filterValue;

                return [
                    'bool' => [
                        'must' => array_map(
                            function (FilterValueInterface $filterValue) use ($searchKey) {
                                return $this->getQueryShapeForSeparatedFilterQuery($searchKey, $filterValue);
                            },
                            $intersectionValue->getValues()
                        )
                    ],
                ];
            case FilterValueInterface::TYPE_RANGE:
                /** @var RangeFilterValueInterface $rangeValue */
                $rangeValue = $filterValue;

                return [
                    'range' => [
                        $searchKey => [
                            'gte' => $rangeValue->getMin(),
                            'lte' => $rangeValue->getMax(),
                        ],
                    ],
                ];
            default:
                return null;
                break;
        }
    }
}
