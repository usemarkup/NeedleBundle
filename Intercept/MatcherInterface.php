<?php

namespace Markup\NeedleBundle\Intercept;

/**
 * An interface for an object that is able to match a query string and determine a match.
 **/
interface MatcherInterface
{
    /**
     * Gets whether the passed query string is a match.
     *
     * @param  string $queryString
     * @return bool
     **/
    public function matches($queryString);
}
