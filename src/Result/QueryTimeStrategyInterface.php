<?php

namespace Markup\NeedleBundle\Result;

/**
 * An interface for a strategy for retrieving a query time for a result.
 **/
interface QueryTimeStrategyInterface
{
    /**
     * @return float
     **/
    public function getQueryTimeInMilliseconds();
}
