<?php

namespace Markup\NeedleBundle\Adapter;

use GuzzleHttp\Promise\PromiseInterface;
use Pagerfanta\Adapter\AdapterInterface;
use Solarium\QueryType\Select\Result\Result;

class SolariumResultPromisePagerfantaAdapter implements AdapterInterface
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
     * @return int The number of results.
     */
    public function getNbResults()
    {
        return $this->getResult()->getNumFound();
    }

    /**
     * Returns an slice of the results.
     *
     * @param int $offset The offset.
     * @param int $length The length.
     *
     * @return array|\Traversable The slice.
     */
    public function getSlice($offset, $length)
    {
        // ignore offset/length as that should have been predetermined
        return $this->getResult();
    }

    /**
     * @return Result
     */
    private function getResult()
    {
        return $this->resultPromise->wait();
    }
}
