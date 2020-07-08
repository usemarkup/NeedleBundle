<?php

namespace Markup\NeedleBundle\Service;

use Markup\NeedleBundle\Context\SearchContextInterface;
use Markup\NeedleBundle\Result\ResultInterface;

/**
 * An interface for a search service.
 **/
interface SearchServiceInterface
{
    public function executeQuery($query, ?SearchContextInterface $searchContext = null): ResultInterface;
}
