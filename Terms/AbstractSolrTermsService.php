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

    public function __construct(
        Solarium $solarium,
        TermsFieldProviderInterface $fieldProvider,
        LoggerInterface $logger = null
    ) {
        $this->solarium = $solarium;
        $this->fieldProvider = $fieldProvider;
        $this->logger = $logger ?? new NullLogger();
    }
}
