<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Service;

use Symfony\Component\DependencyInjection\ServiceLocator;

class SearchServiceLocator extends ServiceLocator implements SearchServiceLocatorInterface
{
    public function fetchServiceForCorpus(string $corpus): SearchServiceInterface
    {
        /**
         * The SearchServiceInterface has the potential to be holding state
         * due to setters so for now we clone the object to safety ensure
         * nothing to held between fetches
         */
        return clone $this->get($corpus);
    }
}
