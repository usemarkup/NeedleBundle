<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Result;

/**
 * Null implementation of a search result.
 */
class NullResult implements ResultInterface
{
    public function getIterator()
    {
        return new \ArrayIterator();
    }

    public function count()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryTimeInMilliseconds()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbPages()
    {
        return 1;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentPage()
    {
        return 1;
    }

    /**
     * {@inheritdoc}
     */
    public function haveToPaginate()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPreviousPage()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviousPage()
    {
        throw new PageDoesNotExistException();
    }

    /**
     * {@inheritdoc}
     */
    public function hasNextPage()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getNextPage()
    {
        throw new PageDoesNotExistException();
    }

    /**
     * {@inheritdoc}
     */
    public function getFacetSets()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getSpellcheckResult()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasDebugOutput()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getDebugOutput()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getMappingHashForFields(): array
    {
        return [];
    }

    public function getMaxPerPage(): int
    {
        return 1;
    }
}
