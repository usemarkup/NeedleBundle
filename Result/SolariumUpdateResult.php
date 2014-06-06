<?php

namespace Markup\NeedleBundle\Result;

use Solarium\QueryType\Update\Result;

class SolariumUpdateResult implements UpdateResultInterface
{
    /**
     * @var Result
     */
    private $result;

    /**
     * @param Result $result
     */
    public function __construct(Result $result)
    {
        $this->result = $result;
    }

    /**
     * Gets whether the update was successful.
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return intval($this->result->getStatus()) === 0;
    }

    /**
     * Gets whichever status code it is that the backend emits.
     *
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->result->getStatus();
    }

    /**
     * @return float|int
     */
    public function getQueryTimeInMilliseconds()
    {
        return $this->result->getQueryTime();
    }
}
