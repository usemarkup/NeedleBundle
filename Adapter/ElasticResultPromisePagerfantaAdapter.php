<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Adapter;

use GuzzleHttp\Promise\PromiseInterface;
use Pagerfanta\Adapter\AdapterInterface;

class ElasticResultPromisePagerfantaAdapter implements AdapterInterface
{
    /**
     * @var PromiseInterface
     */
    private $resultPromise;

    public function __construct(PromiseInterface $resultPromise)
    {
        $this->resultPromise = $resultPromise;
    }

    /**
     * Returns the number of results.
     *
     * @return integer The number of results.
     */
    public function getNbResults()
    {
        return $this->getResult()['hits']['total'] ?? 0;
    }

    /**
     * Returns an slice of the results.
     *
     * @param integer $offset The offset.
     * @param integer $length The length.
     *
     * @return array|\Traversable The slice.
     */
    public function getSlice($offset, $length)
    {
        // ignore offset/length as that should have been predetermined
        return array_filter(array_map(
            function ($result) {
                return (object) $result['_source'] ?? null;
            },
            $this->getResult()['hits']['hits'] ?? []
        ));
    }

    private function getResult(): array
    {
        return $this->resultPromise->wait();
    }
}
