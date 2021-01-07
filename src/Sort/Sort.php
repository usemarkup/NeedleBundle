<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Sort;

use Markup\NeedleBundle\Attribute\AttributeInterface;

/**
* A simple sort.
*/
class Sort implements SortInterface
{
    /**
     * @var AttributeInterface
     **/
    private $filter;

    /**
     * @var bool
     **/
    private $isDescending;

    public function __construct(AttributeInterface $filter, bool $isDescending = false)
    {
        $this->filter = $filter;
        $this->isDescending = $isDescending;
    }

    /**
     * {@inheritdoc}
     **/
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * {@inheritdoc}
     **/
    public function isDescending()
    {
        return $this->isDescending;
    }
}
