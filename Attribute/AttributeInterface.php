<?php

namespace Markup\NeedleBundle\Attribute;

use Markup\NeedleBundle\Exception\UnformableSearchKeyException;

/**
 * Base interface for an attribute that can be filtered or faceted on.
 *
 * NB. Will replace deprecated filter and facet interfaces.
 */
interface AttributeInterface
{
    /**
     * The name by which this attribute is referred within the application.
     *
     * @return string
     **/
    public function getName();

    /**
     * The name by which this attribute should be referred to in visible output.
     *
     * @return string
     **/
    public function getDisplayName();

    /**
     * The key being used for this attribute in a search on a search engine.
     *
     * @param array $options  Options that might determine which key is returned. Possible values: 'prefer_parsed'
     * @return string
     * @throws UnformableSearchKeyException
     **/
    public function getSearchKey(array $options = []);

    /**
     * Magic toString method.  Returns display name.
     *
     * @return string
     **/
    public function __toString();
}
