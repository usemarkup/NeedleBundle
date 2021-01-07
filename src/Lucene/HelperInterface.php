<?php

namespace Markup\NeedleBundle\Lucene;

/**
 * An interface for an object that can assemble a Lucene expression with appropriate escaping.
 **/
interface HelperInterface
{
    /**
     * Assemble a querystring with placeholders
     *
     * These placeholder modes are supported:
     * %1% = no mode, will default to literal
     * %L2% = literal
     * %P3% = phrase-escaped
     * %T4% = term-escaped
     *
     * Numbering starts at 1, so number 1 refers to the first entry
     * of $parts (which has array key 0)
     * You can use the same part multiple times, even in multiple modes.
     * The mode letters are not case sensitive.
     *
     * @param string $query
     * @param array  $parts
     *
     * @return string
     **/
    public function assemble($query, $parts);
}
