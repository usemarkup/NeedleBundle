<?php

namespace Markup\NeedleBundle\Adapter;

use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Exception\InvalidArgumentException;
use Solarium\Client;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Select\Result\Result;

/**
 * SolariumAdapter specifically for supporting grouped results. Should only be used with 'grouped' queries.
 * This only works with a very specific grouping configuration, and this is checked on initialization.
 */
class SolariumGroupedQueryPagerfantaAdapter implements AdapterInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Query
     */
    private $query;

    /**
     * @var GroupedResultAdapter|null
     */
    private $resultSet;

    /**
     * @var string
     */
    private $endPoint;

    /**
     * @var int|null
     */
    private $resultSetStart;

    /**
     * @var int|null
     */
    private $resultSetRows;

    /**
     * @param Client      $client A Solarium client.
     * @param Query $query  A Solarium select query.
     */
    public function __construct($client, $query)
    {
        if (count($query->getGrouping()->getFields()) !== 1) {
            throw new \InvalidArgumentException('This adapter only handles grouped queries with exactly one field grouping only');
        }
        if ($query->getGrouping()->getMainResult() !== false) {
            throw new \InvalidArgumentException('This adapter only handles grouped queries where main result is false');
        }
        $this->checkClient($client);
        $this->checkQuery($query);

        $this->client = $client;
        $this->query = $query;
    }

    private function checkClient($client)
    {
        if ($this->isClientInvalid($client)) {
            throw new InvalidArgumentException($this->getClientInvalidMessage($client));
        }
    }

    private function isClientInvalid($client)
    {
        return !($client instanceof Client);
    }

    private function getClientInvalidMessage($client)
    {
        return sprintf(
            'The client object should be a Solarium\Core\Client\Client instance, %s given',
            get_class($client)
        );
    }

    private function checkQuery($query)
    {
        if ($this->isQueryInvalid($query)) {
            throw new InvalidArgumentException($this->getQueryInvalidMessage($query));
        }
    }

    private function isQueryInvalid($query)
    {
        return !($query instanceof Query);
    }

    private function getQueryInvalidMessage($query)
    {
        return sprintf(
            'The query object should be a Solarium\QueryType\Select\Query\Query instance, %s given',
            get_class($query)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        $resultSet = $this->getResultSet();

        return $resultSet->getNumFound();
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        return $this->getResultSet($offset, $length);
    }

    /**
     * @param int $start
     * @param int $rows
     *
     * @return GroupedResultAdapter
     **/
    public function getResultSet($start = null, $rows = null)
    {
        if ($this->resultSetStartAndRowsAreNotNullAndChange($start, $rows)) {
            $this->resultSetStart = $start;
            $this->resultSetRows = $rows;

            $this->modifyQuery();
            $this->clearResultSet();
        }

        if (null === $this->resultSet) {
            $this->resultSet = $this->createResultSet();
        }

        return $this->resultSet;
    }

    private function resultSetStartAndRowsAreNotNullAndChange($start, $rows)
    {
        return $this->resultSetStartAndRowsAreNotNull($start, $rows) &&
        $this->resultSetStartAndRowsChange($start, $rows);
    }

    private function resultSetStartAndRowsAreNotNull($start, $rows)
    {
        return $start !== null && $rows !== null;
    }

    private function resultSetStartAndRowsChange($start, $rows)
    {
        return $start !== $this->resultSetStart || $rows !== $this->resultSetRows;
    }

    private function modifyQuery()
    {
        if (null !== $this->resultSetStart) {
            $this->query->setStart($this->resultSetStart);
        }
        if (null !== $this->resultSetRows) {
            $this->query->setRows($this->resultSetRows);
        }
    }

    private function createResultSet()
    {
        $resultSet = $this->client->select($this->query, $this->endPoint);

        return new GroupedResultAdapter($resultSet);
    }

    private function clearResultSet()
    {
        $this->resultSet = null;
    }

    public function setEndPoint($endPoint)
    {
        $this->endPoint = $endPoint;

        return $this;
    }
}
