<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Service;

use Markup\NeedleBundle\Context\SearchContextInterface;
use Markup\NeedleBundle\Query\ResolvedSelectQueryDecoratorInterface;
use Markup\NeedleBundle\Query\SelectQueryInterface;
use Markup\NeedleBundle\Result\NullResult;

/**
 * No-op implementation of a search service.
 */
class NoopSearchService implements SearchServiceInterface
{
    /**
     * Executes a select query on a service and returns a result.
     *
     * @param SelectQueryInterface $query
     * @return \Markup\NeedleBundle\Result\ResultInterface
     **/
    public function executeQuery(SelectQueryInterface $query)
    {
        return new NullResult();
    }

    /**
     * Sets a context on the search service, which is a contextual object that can determine aspects of the search to execute, agnostic of the actual search implementation.
     *
     * @param SearchContextInterface $context
     **/
    public function setContext(SearchContextInterface $context)
    {
        return;
    }

    /**
     * Adds a decorator that will decorate the ResolvedSelectQuery during execution
     * directly after the SelectQuery has been combined with the SearchContext
     *
     * @param ResolvedSelectQueryDecoratorInterface $decorator
     **/
    public function addDecorator(ResolvedSelectQueryDecoratorInterface $decorator)
    {
        return;
    }
}
