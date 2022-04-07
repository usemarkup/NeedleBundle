<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Lucene;

/**
 * An object that can normalize/ transform a literal search term into a form to be passed to a backend engine using Lucene syntax.
 */
class SearchTermProcessor
{
    /**
     * This filter cannot be combined with others using bitwise logic - that would make no sense anyway.
     */
    const FILTER_NONE = 0;

    /**
     * These filters can be combined to form a bitmask.
     */
    const FILTER_NORMALIZE = 1;
    const FILTER_FUZZY_MATCHING = 2;

    public function process(string $text, int $filterBitMask = self::FILTER_NORMALIZE): string
    {
        if ($filterBitMask === self::FILTER_NONE) {
            return $text;
        }

        if ($filterBitMask & self::FILTER_NORMALIZE) {
            $text = trim(preg_replace('/[\s~]+/', ' ', $text) ?? $text);
            $text = trim(preg_replace('/[\:/', '\:', $text) ?? $text);
        }

        if ($filterBitMask & self::FILTER_FUZZY_MATCHING) {
            $text = preg_replace(
                [
                    '/("[\pL\s]+"|\pL+~*)/u',
                    '/~+/'
                ],
                [
                    '$1~',
                    '~'
                ],
                $text
            ) ?? $text;
        }

        return $text;
    }
}
