<?php

namespace Markup\NeedleBundle\Exception;

/**
 * An exception representing when an attribute was expected to be available from an attribute provider, but was not.
 */
class MissingAttributeException extends \RuntimeException implements ExceptionInterface
{

}
