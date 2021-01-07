<?php

namespace Markup\NeedleBundle\Exception;

use InvalidArgumentException;

/**
* An exception signifying that a subject passed is of an invalid type for a context (i.e. a particular corpus).
*/
class InvalidSubjectTypeException extends InvalidArgumentException implements ExceptionInterface
{
    public function __construct($message = null)
    {
        if (null === $message) {
            $message = 'The passed subject was not of the expected type.';
        }
        parent::__construct($message);
    }
}
