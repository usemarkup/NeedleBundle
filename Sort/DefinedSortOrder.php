<?php

namespace Markup\NeedleBundle\Sort;

class DefinedSortOrder implements \Countable
{
    /**
     * @var string
     */
    private $fieldName;

    /**
     * @var array
     */
    private $values;

    public function __construct(string $fieldName, array $values)
    {
        $this->fieldName = $fieldName;
        // force zero indexing
        $this->values = array_values($values);
    }
    
    public function getFieldName(): string
    {
        return $this->fieldName;
    }
    
    public function getValues(): array
    {
        return $this->values;
    }

    public function count()
    {
        return count($this->values);
    }
}
