<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Client;

use Symfony\Component\DependencyInjection\ServiceLocator;

class BackendClientServiceLocator extends ServiceLocator
{
    public function fetchClientForCorpus(string $corpus)
    {
        return $this->get($corpus);
    }
}
