<?php

namespace Markup\NeedleBundle\Result;

use Solarium\QueryType\Select\Result\Result as SolariumResult;

/**
* A strategy for retrieving a query time that uses a Solarium result.
*/
class SolariumQueryTimeStrategy implements QueryTimeStrategyInterface
{
    /**
     * A Solarium select result instance.
     *
     * @var SolariumResult
     **/
    private $solariumResult;

    /**
     * @param SolariumResult $result
     **/
    public function __construct(SolariumResult $result)
    {
        $this->solariumResult = $result;
    }

    public function getQueryTimeInMilliseconds()
    {
        return floatval($this->getSolariumResult()->getQueryTime());
    }

    /**
     * @return SolariumResult
     **/
    private function getSolariumResult()
    {
        return $this->solariumResult;
    }
}
