<?php

namespace Markup\NeedleBundle\Service;

use GuzzleHttp\Promise\PromiseInterface;
use Markup\NeedleBundle\Query\SelectQueryInterface;

interface AsyncSearchServiceInterface extends SearchServiceInterface
{
    /**
     * Gets a promise which, when being resolved, executes a select query on a service and makes a result available.
     *
     * @param SelectQueryInterface $query
     * @return PromiseInterface
     **/
    public function executeQueryAsync(SelectQueryInterface $query);
}
