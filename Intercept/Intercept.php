<?php

namespace Markup\NeedleBundle\Intercept;

/**
* An individual search intercept.
*/
class Intercept implements InterceptInterface
{
    /**
     * @var string
     **/
    private $uri;

    /**
     * @param string $uri
     **/
    public function __construct($uri)
    {
        $this->uri = $uri;
    }

    /**
     * {@inheritdoc}
     **/
    public function getUri()
    {
        return $this->uri;
    }
}
