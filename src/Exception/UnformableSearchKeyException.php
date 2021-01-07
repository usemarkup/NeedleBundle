<?php

namespace Markup\NeedleBundle\Exception;

/**
 * An exception representing when a search key cannot be formed, perhaps because there is missing context information.
 */
class UnformableSearchKeyException extends \RuntimeException implements ExceptionInterface
{

}
