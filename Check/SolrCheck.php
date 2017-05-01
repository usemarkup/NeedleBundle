<?php

namespace Markup\NeedleBundle\Check;

use Solarium\Client as Solarium;
use Solarium\Exception\ExceptionInterface as SolariumException;
use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\Success;

/**
* A check object for checking whether Solr is accessible from this PHP application.
*/
class SolrCheck implements CheckInterface
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
            return new Failure(sprintf('Ping on Solr failed with message: "%s"', $e->getMessage()));
        }

        return new Success('OK');
    }

    public function getLabel()
    {
        return 'Solr ping';
    }

    /**
     * @return Solarium
     **/
    private function getSolariumClient()
    {
        return $this->solarium;
    }
}
