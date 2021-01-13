<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\DataCollector;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Solarium\Core\Client\Endpoint as SolariumEndpoint;
use Solarium\Core\Client\Request as SolariumRequest;
use Solarium\Core\Client\Response as SolariumResponse;
use Solarium\Core\Event\Events as SolariumEvents;
use Solarium\Core\Event\PostExecuteRequest as SolariumPostExecuteRequestEvent;
use Solarium\Core\Event\PreExecuteRequest as SolariumPreExecuteRequestEvent;
use Solarium\Core\Plugin\AbstractPlugin as SolariumPlugin;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

/**
 * Copied from Nelmio\SolariumBundle\Logger and modified.
 */
class SolrDataCollector extends SolariumPlugin implements DataCollectorInterface, \Serializable
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $queries = [];

    /**
     * @var SolariumRequest|null
     */
    protected $currentRequest;

    /**
     * @var float|null
     */
    protected $currentStartTime;

    /**
     * @var SolariumEndpoint|null
     */
    protected $currentEndpoint;

    /**
     * @var EventDispatcherInterface[]
     */
    protected $eventDispatchers = [];

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var string
     */
    protected $solrDashboardDomain;

    /**
     * SolrDataCollector constructor.
     * @param array|null $options
     */
    public function __construct($options = null)
    {
        $this->logger = new NullLogger();
        parent::__construct($options);
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function setSolrDashboardDomain(string $solrDashboardDomain): void
    {
        $this->solrDashboardDomain = $solrDashboardDomain;
    }

    /**
     * Parse the solr queries logged and save for use in debugging activities.
     *
     * @param HttpRequest $request
     * @param HttpResponse $response
     * @param \Exception|null $exception
     */
    public function collect(HttpRequest $request, HttpResponse $response, \Exception $exception = null): void
    {
        if (isset($this->currentRequest)) {
            $this->failCurrentRequest();
        }

        $time = 0.0;
        foreach ($this->queries as $queryStruct) {
            $time += $queryStruct['durationMs'];
        }
        $this->data = [
            'queries' => $this->queries,
            'totalTimeMs' => $time,
        ];
    }

    public function getName(): string
    {
        return 'solr';
    }

    public function reset()
    {
        $this->data = [];
    }

    public function preExecuteRequest(SolariumPreExecuteRequestEvent $event): void
    {
        if (isset($this->currentRequest)) {
            $this->failCurrentRequest();
        }

        $this->currentRequest = $event->getRequest();
        $this->currentEndpoint = $event->getEndpoint();

        $this->logger->debug($event->getEndpoint()->getBaseUri().$this->currentRequest->getUri());
        $this->currentStartTime = microtime(true);
    }

    public function postExecuteRequest(SolariumPostExecuteRequestEvent $event): void
    {
        $endTime = microtime(true) - $this->currentStartTime;
        if (!isset($this->currentRequest)) {
            throw new \RuntimeException('Request not set');
        }
        if ($this->currentRequest !== $event->getRequest()) {
            throw new \RuntimeException('Requests differ');
        }

        $this->log($event->getRequest(), $event->getResponse(), $event->getEndpoint(), $endTime);

        $this->currentRequest = null;
        $this->currentStartTime = null;
        $this->currentEndpoint = null;
    }

    public function getQueryCount(): int
    {
        return count($this->getQueries());
    }

    public function getQueries(): array
    {
        return array_key_exists('queries', $this->data) ? $this->data['queries'] : [];
    }

    public function getTotalTimeMs(): float
    {
        return array_key_exists('totalTimeMs', $this->data) ? $this->data['totalTimeMs'] : 0.0;
    }

    public function serialize(): ?string
    {
        return serialize($this->data);
    }

    public function unserialize($serialized): void
    {
        $this->data = unserialize($serialized);
    }

    /**
     * Plugin init function
     *
     * Register event listeners
     */
    protected function initPluginType()
    {
        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = $this->client->getEventDispatcher();
        if (!in_array($dispatcher, $this->eventDispatchers, true)) {
            $dispatcher->addListener(SolariumEvents::PRE_EXECUTE_REQUEST, [$this, 'preExecuteRequest'], 1000);
            $dispatcher->addListener(SolariumEvents::POST_EXECUTE_REQUEST, [$this, 'postExecuteRequest'], -1000);
            $this->eventDispatchers[] = $dispatcher;
        }
    }

    protected function log(
        SolariumRequest $request,
        ?SolariumResponse $response,
        SolariumEndpoint $endpoint,
        float $durationSec
    ): void {
        $requestUri = str_replace(
            '://localhost:',
            sprintf('://%s:', $this->solrDashboardDomain),
            $endpoint->getBaseUri().$request->getUri()
        );

        $isPost = !empty($request->getRawData());
        $requestParams = $request->getParams();
        if ($isPost) {
            $requestParams = $this->normalizePostParams($requestParams, $request->getRawData());
        }

        $responseBody = $response ? $response->getBody() : null;
        if ($responseBody) {
            $jsonBody = json_decode($responseBody);
            if (!empty($jsonBody)) {
                $responseBody = str_replace(
                    '\"',
                    '"',
                    json_encode($jsonBody, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: ''
                );
            }
        }

        $this->queries[] = [
            'isPost' => $isPost,
            'requestUri' => $requestUri,
            'requestParams' => $requestParams,
            'statusCode' => $response ? $response->getStatusCode() : null,
            'responseBody' => $responseBody,
            'durationMs' => $durationSec * 1000,
        ];
    }

    protected function failCurrentRequest(): void
    {
        $endTime = microtime(true) - $this->currentStartTime;
        if (!$this->currentRequest || !$this->currentEndpoint) {
            throw new \RuntimeException('Incorrect fail method usage.');
        }
        $this->log($this->currentRequest, null, $this->currentEndpoint, $endTime);

        $this->currentRequest = null;
        $this->currentStartTime = null;
        $this->currentEndpoint = null;
    }

    protected function normalizePostParams(array $requestParams, string $rawData): array
    {
        $postParams = explode('&', $rawData);
        foreach ($postParams as $oneParam) {
            list($paramName, $paramValue) = explode('=', $oneParam, 2);
            $paramValue = urldecode($paramValue);
            if (array_key_exists($paramName, $requestParams)) {
                if (!is_array($requestParams[$paramName])) {
                    $requestParams[$paramName] = [$requestParams[$paramName]];
                }
                $requestParams[$paramName][] = $paramValue;
            } else {
                $requestParams[$paramName] = $paramValue;
            }
        }
        return $requestParams;
    }
}
