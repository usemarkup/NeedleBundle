<?php

namespace Markup\NeedleBundle\Service;

use GuzzleHttp\Promise\PromiseInterface;
use Markup\NeedleBundle\Context\SearchContextInterface;
use Markup\NeedleBundle\Query\ResolvedSelectQueryInterface;
use Markup\NeedleBundle\Query\SelectQueryInterface;

interface AsyncSearchServiceInterface extends SearchServiceInterface
{
    /**
     * Gets a promise which, when being resolved, executes a select query on a service and makes a result available.
     *
     * @param SelectQueryInterface|ResolvedSelectQueryInterface $query
     * @param SearchContextInterface|null $searchContext
     * @return PromiseInterface
     */
    public function executeQueryAsync($query, ?SearchContextInterface $searchContext = null);
}
