<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Service;

/**
 * An interface providing a reliable means for consuming code to fetch a search service for a corpus.
 */
interface SearchServiceLocatorInterface
{
    public function fetchServiceForCorpus(string $corpus): SearchServiceInterface;
}
