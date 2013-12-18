<?php

namespace Markup\NeedleBundle\Intercept;

/**
 * An interface for an object that can match a search query to an intercept.
 **/
interface InterceptorInterface
{
    /**
     * Matches a provided search query. Returns an intercept if match happens, null otherwise.
     *
     * @param  string                  $queryString
     * @return InterceptInterface|null
     **/
    public function matchQueryToIntercept($queryString);
}
