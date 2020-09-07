<?php

namespace Markup\NeedleBundle\Query;

/**
* A simple query implementation.
*/
class SimpleQuery implements SimpleQueryInterface
{
    /**
     * @var string
     **/
    private $term;

    /**
     * @param string $term
     **/
    public function __construct($term = '')
    {
        $this->term = strval($term);
    }

    /**
     * {@inheritdoc}
     **/
    public function getSearchTerm()
    {
        return $this->term;
    }

    /**
     * {@inheritdoc}
     **/
    public function hasSearchTerm(): bool
    {
        return '' !== $this->term;
    }
}
