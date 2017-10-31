<?php
namespace Markup\NeedleBundle\Filter\Field;

/**
 * Class RangeFilterField
 * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/_search_operations.html
 * @package Markup\NeedleBundle\Filter\Field
 */
class RangeFilterField
{
    /**
     * @var string
     */
    private $fieldName;

    /**
     * @var string|null
     */
    private $minValue;

    /**
     * @var string|null
     */
    private $maxValue;

    /**
     * RangeFilterField constructor.
     * @param string $fieldName
     * @param array $value => ['min' => yourMinValue, 'max' => yourMaxValue]
     */
    public function __construct(string $fieldName, array $value)
    {
        $this->fieldName = $fieldName;

        $this->prepareRangeValues($value);
    }

    /**
     * @return array|null
     */
    public function getQuery(): ?array
    {
        $rangeValues = $this->getRangeValues();

        if (!$rangeValues) {
            return null;
        }

        return [
            'bool' => [
                'must' =>
                    [
                        [
                            'range' =>
                                [
                                    $this->fieldName => $rangeValues
                                ]
                        ]
                    ]
            ]
        ];
    }

    /**
     * @param array $value
     */
    private function prepareRangeValues(array $value)
    {
        if (isset($value['min']) && $value['min']) {
            // Required for filtering by DateTime range
            if ($value['min'] instanceof \DateTime) {
                $this->minValue = $value['min']->getTimestamp();
            } else {
                $this->minValue = $value['min'];
            }
        }


        if (isset($value['max']) && $value['max']) {
            // Required for filtering by DateTime range
            if ($value['max'] instanceof \DateTime) {
                $this->maxValue = $value['max']->getTimestamp();
            } else {
                $this->maxValue = $value['max'];
            }
        }
    }

    /**
     * @return array|null
     */
    private function getRangeValues()
    {
        $result = [];
        if ($this->minValue) {
            $result['gte'] = $this->minValue;
        }
        if ($this->maxValue) {
            $result['lte'] = $this->maxValue;
        }

        if (empty($result)) {
            return null;
        }

        return $result;
    }
}
