<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Elastic;

/**
 * A corpus keyed array of mappings and settings by Corpus
 */
class CorpusIndexConfiguration
{
    /**
     * @var array
     */
    private $settings;

    /**
     * @var array
     */
    private $mappings;

    public function __construct(?array $settings = null, ?array $mappings = null)
    {
        $this->settings = $settings ?: [];
        $this->mappings = $mappings ?: [];
    }

    public function getSettings(string $corpus): array
    {
        return $this->settings[$corpus] ?? [];
    }

    public function getMappings(string $corpus): array
    {
        return $this->mappings[$corpus] ?? [];
    }
}
