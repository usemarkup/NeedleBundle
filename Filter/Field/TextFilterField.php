<?php
namespace Markup\NeedleBundle\Filter\Field;

/**
 *
 * Class TextFilterField
 * @see https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/_search_operations.html
 * @package Markup\NeedleBundle\Filter\Field
 */
class TextFilterField
{
    /**
     * @var string
     */
    private $fieldName;

    /**
     * @var string
     */
    private $value;

    public function __construct(string $fieldName, string $value)
    {
        $this->fieldName = $fieldName;
        $this->value = $value;
    }

    /**
     * @return array
     */
    public function getQuery(): array
    {
        return [
            'bool' => [
                'must' =>
                    [
                        [
                            'match' =>
                                [
                                    $this->fieldName => $this->value
                                ]
                        ]
                    ]
            ]
        ];
    }
}
