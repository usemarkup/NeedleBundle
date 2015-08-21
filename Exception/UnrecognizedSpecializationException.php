<?php

namespace Markup\NeedleBundle\Exception;

/**
 * An exception when a specialization is referenced that is not recognized
 */
class UnrecognizedSpecializationException extends \InvalidArgumentException implements ExceptionInterface
{
}
