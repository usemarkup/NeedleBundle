<?php

namespace Markup\NeedleBundle\Suggest;

use Markup\NeedleBundle\Query\SimpleQueryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Solarium\Client as Solarium;
use Solarium\Exception\ExceptionInterface as SolariumException;

/**
 * A suggest service using Solr/Solarium.
 */
class SolrSuggestService implements SuggestServiceInterface
{
    /**
     * @var Solarium
     */
    private $solarium;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Solarium $solarium
     * @param LoggerInterface $logger
     */
    public function __construct(Solarium $solarium, LoggerInterface $logger = null)
    {
        $this->solarium = $solarium;
        $this->setLogger($logger);
    }

    /**
     * @param SimpleQueryInterface $query
     * @return SuggestResultInterface
     */
    public function fetchSuggestions(SimpleQueryInterface $query)
    {
        $suggestQuery = $this->solarium->createSuggester();
        $suggestQuery->setQuery($query->getSearchTerm());
        $suggestQuery->setDictionary('suggest');
        $suggestQuery->setCount(20);
        $suggestQuery->setCollate(true);

        try {
            $resultSet = $this->solarium->suggester($suggestQuery);
        } catch (SolariumException $e) {
            $this->logger->critical('A suggest query did not complete successfully. Have you enabled the suggest Solr component?');

            return new EmptySuggestResult();
        }

        return new SolrSuggestResult($resultSet);
    }

    /**
     * @param LoggerInterface $logger
     * @return self
     */
    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger ?: new NullLogger();

        return $this;
    }
}
