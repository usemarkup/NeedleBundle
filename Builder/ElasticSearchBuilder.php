<?php

namespace Markup\NeedleBundle\Builder;

use Markup\NeedleBundle\Filter\Field\ArrayFilterField;
use Markup\NeedleBundle\Filter\Field\ExistsFilterField;
use Markup\NeedleBundle\Filter\Field\RangeFilterField;
use Markup\NeedleBundle\Filter\Field\TextFilterField;

class ElasticSearchBuilder implements ElasticDocumentInterface
{
    protected $data = [];
    /**
     * @var string
     */
    private $index;
    /**
     * @var string
     */
    private $type;

    /**
     * private $queryData;
     *
     * /**
     * @var array
     */
    private $arrayTypeFields;

    /**
     * @var array
     */
    private $rangeTypeFields;

    /**
     * @var array
     */
    private $existTypeFields;

    /**
     * @var array
     */
    private $queryData;

    /**
     * @param string $index
     * @param string $type
     */
    public function __construct(string $index, string $type)
    {
        $this->data = [
            'index' => $index,
            'type' => $type
        ];

        $this->index = $index;
        $this->type = $type;
        $this->reset();
    }

    public function build()
    {
        $this->data['body']['query'] = $this->prepareQueries();

        return $this->data;
    }

    /**
     * @return array
     */
    private function prepareQueries()
    {
        if (empty($this->queryData)) {
            return $this->prepareEmptySearchQuery();
        }

        $result['bool'] = [];
        foreach ($this->queryData as $fieldName => $value) {
            if ($this->isArrayType($fieldName)) {
                $field = new ArrayFilterField($fieldName, $value);
            } elseif ($this->isRangeType($fieldName)) {
                $field = new RangeFilterField($fieldName, $value);
            } elseif ($this->isExistType($fieldName)) {
                $field = new ExistsFilterField($fieldName, $value);
            } else {
                $field = new TextFilterField($fieldName, $value);
            }

            if ($field->getQuery()) {
                $result = array_merge_recursive($result, $field->getQuery());
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    private function prepareEmptySearchQuery(): array
    {
        return [
            'match_all' => new \stdClass()
        ];
    }

    /**
     * @param $field
     * @param $value
     */
    public function addQueryData($field, $value)
    {
        $this->queryData[$field] = $value;
    }

    /**
     * @param string $fieldName
     */
    public function addArrayTypeField(string $fieldName)
    {
        array_push($this->arrayTypeFields, $fieldName);
    }

    /**
     * @param string $fieldName
     */
    public function addRangeTypeField(string $fieldName)
    {
        array_push($this->rangeTypeFields, $fieldName);
    }

    /**
     * @param string $fieldName
     */
    public function addExistTypeField(string $fieldName)
    {
        array_push($this->existTypeFields, $fieldName);
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    public function isRangeType(string $fieldName)
    {
        return in_array($fieldName, $this->rangeTypeFields);
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    public function isArrayType(string $fieldName)
    {
        return in_array($fieldName, $this->arrayTypeFields);
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    public function isExistType(string $fieldName)
    {
        return in_array($fieldName, $this->existTypeFields);
    }

    /**
     * @param int $numberOfItems
     */
    public function setPerPage(int $numberOfItems = 10)
    {
        $this->data['size'] = $numberOfItems;
    }

    /**
     * @param int $page
     */
    public function setPage(int $page = 1)
    {
        $from = ($page - 1) * (int)$this->data['size'];

        $this->data['from'] = $from;
    }

    /**
     * Resets builder data
     */
    public function reset()
    {
        $this->queryData = [];
        $this->arrayTypeFields = [];
        $this->rangeTypeFields = [];
        $this->existTypeFields = [];
    }
}
