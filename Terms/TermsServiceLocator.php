<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Terms;

use Symfony\Component\DependencyInjection\ServiceLocator;

class TermsServiceLocator extends ServiceLocator
{
    public function fetchTermsServiceForCorpus(string $corpus): TermsServiceInterface
    {
        return $this->get($corpus);
    }
}
