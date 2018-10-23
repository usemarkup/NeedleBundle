<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Synonyms;

interface SynonymClientInterface
{
    public function getStoredLocales(): array;

    public function getSynonyms(string $locale): array;

    public function updateSynonyms(string $locale, array $data): bool;
}
