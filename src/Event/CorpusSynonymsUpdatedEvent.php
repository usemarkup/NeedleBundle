<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class CorpusSynonymsUpdatedEvent extends Event
{
    /**
     * @var string
     */
    private $locale;

    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}
