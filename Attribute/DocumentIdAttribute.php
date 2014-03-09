<?php

namespace Markup\NeedleBundle\Attribute;

/**
 * An attribute representing the ID for a document.
 */
class DocumentIdAttribute implements AttributeInterface
{
    /**
     * @var string
     */
    private $key;

    public function __construct($key = 'id')
    {
        $this->key = $key;
    }

    /**
     * The name by which this attribute is referred within the application.
     *
     * @return string
     **/
    public function getName()
    {
        return 'id';
    }

    /**
     * The name by which this attribute should be referred to in visible output.
     *
     * @return string
     **/
    public function getDisplayName()
    {
        return 'ID';
    }

    /**
     * The key being used for this attribute in a search on a search engine.
     *
     * @return string
     **/
    public function getSearchKey(array $options = array())
    {
        return $this->key;
    }

    /**
     * Magic toString method.  Returns display name.
     *
     * @return string
     **/
    public function __toString()
    {
        return $this->getDisplayName();
    }
}
