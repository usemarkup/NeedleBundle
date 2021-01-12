<?php

namespace Markup\NeedleBundle\Event;

use Markup\NeedleBundle\Intercept;
use Symfony\Component\EventDispatcher\Event;

/**
* An event representing an unresolved search intercept.  This is when there is an intercept relevant to a query string, but that cannot be resolved to an intercept with a URI for some reason.
*/
class UnresolvedInterceptEvent extends Event
{
    /**
     * @var Intercept\DefinitionInterface
     **/
    private $definition;

    /**
     * @var string
     **/
    private $queryString;

    /**
     * The message from the unresolved intercept exception.
     *
     * @var string
     **/
    private $exceptionMessage;

    /**
     * @param Intercept\DefinitionInterface $definition
     * @param string                        $queryString
     * @param string                        $exceptionMessage
     **/
    public function __construct(Intercept\DefinitionInterface $definition, $queryString, $exceptionMessage)
    {
        $this->definition = $definition;
        $this->queryString = $queryString;
        $this->exceptionMessage = $exceptionMessage;
    }

    /**
     * Gets the intercept definition pertaining to this unresolved intercept.
     *
     * @return Intercept\DefinitionInterface
     **/
    public function getInterceptDefinition()
    {
        return $this->definition;
    }

    /**
     * Gets the query string that was being used when this unresolved intercept happened.
     *
     * @return string
     **/
    public function getQueryString()
    {
        return $this->queryString;
    }

    /**
     * Gets the message from the unresolved intercept exception.
     *
     * @return string
     **/
    public function getExceptionMessage()
    {
        return $this->exceptionMessage;
    }
}
