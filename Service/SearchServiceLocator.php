<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Service;

use Symfony\Component\DependencyInjection\ServiceLocator;

class SearchServiceLocator extends ServiceLocator implements SearchServiceLocatorInterface
{
    public function fetchServiceForCorpus(string $corpus): SearchServiceInterface
    {
        return $this->get($corpus);
    }
}
