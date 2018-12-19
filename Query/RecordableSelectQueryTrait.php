<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Query;

/**
 * A trait to facilitate adding recordability to a select query.
 */
trait RecordableSelectQueryTrait
{
    /**
     * @var RecordableSelectQueryInterface
     */
    private $record;

    public function record(): void
    {
        $this->record = clone $this;
    }

    public function hasRecord(): bool
    {
        return null !== $this->record;
    }

    public function getRecord(): ?SelectQueryInterface
    {
        return $this->record;
    }

    /**
     * A method to be used when e.g. cloning an object.
     */
    private function resetRecord(): void
    {
        $this->record = null;
    }
}
