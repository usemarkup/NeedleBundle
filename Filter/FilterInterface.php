<?php

namespace Markup\NeedleBundle\Filter;

/**
 * An interface for a search filter.
 **/
interface FilterInterface
{
    /**
     * The name by which this filter is referred within the application.
     *
     * @return string
     **/
    public function getName();

    /**
     * The name by which this filter should be referred to in visible output.
     *
     * @return string
     **/
    public function getDisplayName();

    /**
     * The key being used for this filter in a search on a search engine.
     *
     * @return string
     **/
    public function getSearchKey();
}
