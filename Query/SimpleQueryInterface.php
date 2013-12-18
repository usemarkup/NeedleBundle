<?php

namespace Markup\NeedleBundle\Query;

/**
 * An interface for a simple search query containing only a search term.
 **/
interface SimpleQueryInterface
{
    /**
     * Gets whether there is a search term associated with this query (i.e. typically a human-entered text search).
     *
     * @return bool
     **/
    public function hasSearchTerm();

    /**
     * Gets the search term being used in this query.  Returns false if not specified.
     *
     * @return string|bool
     **/
    public function getSearchTerm();
}
