<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Service;

use Markup\NeedleBundle\Context\SearchContextInterface;
use Markup\NeedleBundle\Result\NullResult;
use Markup\NeedleBundle\Result\ResultInterface;

/**
 * No-op implementation of a search service.
 */
class NoopSearchService implements SearchServiceInterface
{

    /**
     * @inheritDoc
     */
    public function executeQuery($query, ?SearchContextInterface $searchContext = null): ResultInterface
    {
        return new NullResult();
    }
}
