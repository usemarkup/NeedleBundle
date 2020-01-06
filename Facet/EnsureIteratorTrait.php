<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Facet;

trait EnsureIteratorTrait
{
    private function ensureIterator(\Traversable $candidate): \Iterator
    {
        if (!$candidate instanceof \Iterator) {
            return new \IteratorIterator($candidate);
        }

        return $candidate;
    }
}