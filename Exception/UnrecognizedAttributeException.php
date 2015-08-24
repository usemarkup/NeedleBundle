<?php

namespace Markup\NeedleBundle\Exception;

/**
 * An exception when a attribute is referenced that is not recognized
 */
class UnrecognizedAttributeException extends \InvalidArgumentException implements ExceptionInterface
{
}
