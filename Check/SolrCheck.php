<?php

namespace Markup\NeedleBundle\Check;

use Liip\Monitor\Check\Check as BaseCheck;
use Liip\Monitor\Result\CheckResult;
use Solarium\Client as Solarium;
use Solarium\Exception\ExceptionInterface as SolariumException;

/**
* A check object for checking whether Solr is accessible from this PHP application.
*/
class SolrCheck extends BaseCheck
{
    /**
     * A Solarium client instance.
     *
     * @var Solarium
     **/
    private $solarium;

    /**
     * @param Solarium $solarium
     **/
    public function __construct(Solarium $solarium)
    {
        $this->solarium = $solarium;
    }

    public function check()
    {
        $solarium = $this->getSolariumClient();
        $ping = $solarium->createPing();
        try {
            $checkResult = $solarium->ping($ping);
        } catch (SolariumException $e) {
            return $this->buildResult(sprintf('Ping on Solr failed with message: "%s"', $e->getMessage()), CheckResult::CRITICAL);
        }

        return $this->buildResult('OK', CheckResult::OK);
    }

    public function getName()
    {
        return 'Solr ping';
    }

    /**
     * @return SolariumClient
     **/
    private function getSolariumClient()
    {
        return $this->solarium;
    }
}
