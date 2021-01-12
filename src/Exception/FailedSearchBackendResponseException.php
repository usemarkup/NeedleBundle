<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Exception;

use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * An exception of a failed (non-200) HTTP response from the search backend
 */
class FailedSearchBackendResponseException extends \RuntimeException
{
    /**
     * @var ResponseInterface
     */
    private $response;

    public function __construct(ResponseInterface $response, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->response = $response;
    }

    public function getHttpCode(): int
    {
        return $this->response->getStatusCode();
    }
}
