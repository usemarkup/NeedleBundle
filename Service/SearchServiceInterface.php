<?php

namespace Markup\NeedleBundle\Service;

use Markup\NeedleBundle\Query\SelectQueryInterface;
use Markup\NeedleBundle\Context\SearchContextInterface;

/**
 * An interface for a search service.
 **/
interface SearchServiceInterface
{
    /**
     * Executes a select query on a service and returns a result.
     *
     * @param SelectQueryInterface
     * @return \Markup\NeedleBundle\Result\ResultInterface
     **/
    public function executeQuery(SelectQueryInterface $query);

    /**
     * Sets a context on the search service, which is a contextual object that can determine aspects of the search to execute, agnostic of the actual search implementation.
     *
     * @param SearchContextInterface $context
     **/
    public function setContext(SearchContextInterface $context);
}
