<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Suggest;

use Markup\NeedleBundle\Corpus\CorpusBackendProvider;
use Symfony\Component\DependencyInjection\ServiceLocator;

class SuggestServiceLocator extends ServiceLocator
{
    /**
     * @var CorpusBackendProvider
     */
    private $backendProvider;

    public function __construct(array $factories, CorpusBackendProvider $backendProvider)
    {
        parent::__construct($factories);
        $this->backendProvider = $backendProvider;
    }

    public function getSuggesterForCorpus(string $corpus): SuggestServiceInterface
    {
        return $this->get($this->backendProvider->getBackendForCorpus($corpus));
    }
}
