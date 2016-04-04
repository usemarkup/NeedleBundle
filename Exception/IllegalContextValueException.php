<?php

namespace Markup\NeedleBundle\Exception;

/**
 * An exception pertaining to when a context has been given a value that is illegal
 */
class IllegalContextValueException extends \LogicException implements ExceptionInterface
{
    /**
     * @var mixed
     */
    private $value;

    public function __construct($value, $message = '', $code = 0, \Exception $previous = null)
    {
        $this->value = $value;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
