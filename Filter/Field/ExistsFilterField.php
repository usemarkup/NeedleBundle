<?php
namespace Markup\NeedleBundle\Filter\Field;

/**
 * Class ExistsFilterField
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/2.3/query-dsl-exists-query.html
 * @package Markup\NeedleBundle\Filter\Field
 */
class ExistsFilterField
{
    /**
     * @var string
     */
    private $fieldName;

    /**
     * @var bool
     */
    private $value;

    public function __construct(string $fieldName, bool $value)
    {
        $this->fieldName = $this->prepareFieldName($fieldName);
        $this->value = $value;
    }

    /**
     * Checks whether queried value must exist or should be missing
     * @return string
     */
    private function getFilterCondition()
    {
        if ($this->value) {
            return 'must';
        } else {
            return 'must_not';
        }
    }

    /**
     * @return array
     */
    public function getQuery(): array
    {
        return [
            'bool' => [
                $this->getFilterCondition() =>
                    [
                        [
                            'exists' => [
                                'field' => $this->fieldName
                            ]
                        ]
                    ]
            ]
        ];
    }

    /**
     * @param $fieldName
     * @return string
     */
    private function prepareFieldName(string $fieldName)
    {
        if (substr($fieldName, 0, 3) == 'has') {
            return strtolower(substr($fieldName, 3));
        }

        return $fieldName;
    }
}
