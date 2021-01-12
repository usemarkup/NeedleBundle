<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Synonyms;

class NoopSynonymClient implements SynonymClientInterface
{
    public function getStoredLocales(): array
    {
        return [];
    }

    public function getSynonyms(string $locale): array
    {
        return [];
    }

    public function updateSynonyms(string $locale, array $data): bool
    {
        return false;
    }
}
