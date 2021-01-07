<?php

namespace Markup\NeedleBundle\Result;

/**
 * An interface for a strategy for returning debug output for a search result.
 **/
interface DebugOutputStrategyInterface
{
    /**
     * Gets whether there is debug output for this result that could be displayed.
     *
     * @return bool
     **/
    public function hasDebugOutput();

    /**
     * Gets any debug output that could be displayed for this result - likely to be in HTML format, but this interface does not specify.  Returns null if there is no info to output.
     *
     * @return string|null
     **/
    public function getDebugOutput();
}
