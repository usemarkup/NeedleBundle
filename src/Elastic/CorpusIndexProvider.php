<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Elastic;

class CorpusIndexProvider
{
    /**
     * @string|null
     */
    private $prefix;

    public function __construct(?string $prefix)
    {
        $this->prefix = $prefix;
    }

    public function getIndexForCorpus(string $corpus)
    {
        return implode('_', array_filter([$this->prefix, $corpus]));
    }
}
