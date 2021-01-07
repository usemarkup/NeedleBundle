<?php

namespace Markup\NeedleBundle\Spellcheck;

/**
 * Interface for a spellcheck query that can pull back 'did you mean' like terms.
 */
interface SpellcheckInterface
{
    /**
     * Gets the maximum count of spellcheck results, if one is set (otherwise returns null).
     *
     * @return int|null
     */
    public function getResultLimit();

    /**
     * Gets the spellcheck dictionary to use for a query.
     *
     * @return string
     */
    public function getDictionary();
}
