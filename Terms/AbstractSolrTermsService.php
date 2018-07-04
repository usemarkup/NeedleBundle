<?php

namespace Markup\NeedleBundle\Terms;

use Markup\NeedleBundle\Query\SimpleQueryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Solarium\Client as Solarium;

/**
 * A terms service using Solr/Solarium.
 */
abstract class AbstractSolrTermsService implements TermsServiceInterface
{
    /**
     * @var Solarium
     */
    protected $solarium;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var TermsFieldProviderInterface
     */
    protected $fieldProvider;

    /**
     * @param Solarium                    $solarium
     * @param LoggerInterface             $logger
     * @param TermsFieldProviderInterface $fieldProvider
     */
    public function __construct(
        Solarium $solarium,
        LoggerInterface $logger = null,
        TermsFieldProviderInterface $fieldProvider
    ) {
        $this->solarium = $solarium;
        $this->setLogger($logger);
        $this->fieldProvider = $fieldProvider;
    }

    /**
     * @param SimpleQueryInterface $query
     *
     * @return TermsResultInterface
     */
    abstract public function fetchTerms(SimpleQueryInterface $query);

    /**
     * @param LoggerInterface $logger
     *
     * @return self
     */
    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger ?: new NullLogger();

        return $this;
    }
}
