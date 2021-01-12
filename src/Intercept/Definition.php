<?php

namespace Markup\NeedleBundle\Intercept;

/**
* An intercept definition, with a matcher and a means of resolving to a route.
*/
class Definition implements DefinitionInterface
{
    /**
     * @var MatcherInterface
     **/
    private $matcher;

    /**
     * @var string
     **/
    private $name;

    /**
     * @var string
     **/
    private $type;

    /**
     * @var array
     **/
    private $properties;

    /**
     * @param string           $name
     * @param MatcherInterface $matcher
     * @param string           $type
     * @param array            $properties
     **/
    public function __construct($name, MatcherInterface $matcher, $type, array $properties = [])
    {
        $this->name = $name;
        $this->matcher = $matcher;
        $this->type = $type;
        $this->properties = $properties;
    }

    /**
     * {@inheritdoc}
     **/
    public function getMatcher()
    {
        return $this->matcher;
    }

    /**
     * {@inheritdoc}
     **/
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     **/
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     **/
    public function getProperties()
    {
        return $this->properties;
    }
}
