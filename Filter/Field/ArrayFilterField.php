<?php

namespace Markup\NeedleBundle\Filter\Field;

class ArrayFilterField
{

    /**
     * @var string
     */
    private $fieldName;

    private $value;

    public function __construct(string $fieldName, $value)
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
                    'term' => [
                        $this->fieldName => $this->value
                    ]
                ]
            ]
        ];
    }
}
