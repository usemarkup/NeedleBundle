<?php

namespace Markup\NeedleBundle\Builder;

class ElasticDocumentBuilder implements ElasticDocumentInterface
{
    /**
     * @var array
     */
    protected $data;
    /**
     * @var string
     */
    private $index;
    /**
     * @var string
     */
    private $type;

    /**
     * @param string $index
     * @param string $type
     * @param null|string $id
     */
    public function __construct(string $index, string $type, ?string $id)
    {
        $this->data = [
            'index' => $index,
            'type' => $type,
            'body' => []
        ];

        if ($id) {
            $this->data['id'] = $id;
        }

        $this->index = $index;
        $this->type = $type;
    }

    /**
     * @param array $inputData
     */
    public function addData(array $inputData)
    {
        if (isset($this->data['body'])) {
            $this->data['body'] = array_merge($this->data['body'], $inputData);
        }
    }

    /**
     * Supports mapping only for date type field
     * @param $fieldName
     * @param $fieldType
     * @param array|null $overrideValues
     */
    public function addMapping($fieldName, $fieldType, ?array $overrideValues = null)
    {
        switch ($fieldType) {
            case self::FIELD_TYPE_DATE:
                $mapping = $this->addMappingForDate($fieldName, $overrideValues);
                break;
            default:
                return;
        }

        $existingMappings = $this->data['body']['mappings'][$this->type]['properties'] ?? null;

        if ($existingMappings) {
            $existingMappings = array_merge($existingMappings, $mapping);
        } else {
            $existingMappings = $mapping;
        }

        $this->data['body']['mappings'][$this->type]['properties'] = $existingMappings;
    }

    /**
     * @param $fieldName
     * @param array|null $overrideValues - allows overriding default mapping values
     * @return mixed
     */
    private function addMappingForDate($fieldName, ?array $overrideValues = null)
    {
        $mapping[$fieldName] = [
            'type' => self::FIELD_TYPE_DATE,
            'format' => 'basic_date',
            'null_value' => 'NULL'
        ];

        if ($overrideValues) {
            foreach ($overrideValues as $key => $overrideValue) {
                $mapping[$fieldName][$key] = $overrideValue;
            }
        }

        return $mapping;
    }

    public function build()
    {
        return $this->data;
    }
}
