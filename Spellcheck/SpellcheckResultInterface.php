<?php

namespace Markup\NeedleBundle\Spellcheck;

/**
 * A simple spellchecking result interface.
 */
interface SpellcheckResultInterface
{
    /**
     * Gets whether the query for the spellcheck was correctly spelled.
     *
     * @var bool
     */
    public function isCorrectlySpelled();

    /**
     * Gets a list of suggestions,
     *
     * @return Suggestion[]
     */
    public function getSuggestions();
} 
