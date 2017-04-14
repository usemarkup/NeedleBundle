<?php

namespace Markup\NeedleBundle\Suggest;

use Markup\NeedleBundle\Query\HandlerProviderInterface;
use Markup\NeedleBundle\Query\NullHandlerProvider;
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
     * @var HandlerProviderInterface
     */
    private $handlerProvider;

    /**
     * @param Solarium $solarium
     * @param LoggerInterface $logger
     * @param HandlerProviderInterface $handlerProvider
     */
    public function __construct(
        Solarium $solarium,
        LoggerInterface $logger = null,
        HandlerProviderInterface $handlerProvider = null
    ) {
        $this->solarium = $solarium;
        $this->setLogger($logger);
        $this->handlerProvider = $handlerProvider ?: new NullHandlerProvider();
    }

    /**
     * @param SimpleQueryInterface $query
     * @return SuggestResultInterface[]
     */
    public function fetchSuggestions(SimpleQueryInterface $query)
    {
        $suggestQuery = $this->solarium->createSuggester();
        $suggestQuery->setQuery($query->getSearchTerm());
        $handler = $this->handlerProvider->getHandler();
        if ($handler) {
            $suggestQuery->setHandler($handler);
        }
        $suggestQuery->setDictionary('suggest');
        $suggestQuery->setCount(20);
        $suggestQuery->setCollate(true);

        try {
            $resultSet = $this->solarium->suggester($suggestQuery);
        } catch (SolariumException $e) {
            $this->logger->critical(
                'A suggest query did not complete successfully. Have you enabled the suggest Solr component?',
                ['message' => $e->getMessage()]
            );

            return new EmptySuggestResult();
        }
        //check if this is a grouped result, and use grouped result parser in this case
        if (array_key_exists('grouped', $resultSet->getData())) {
            $groupedResultParser = new GroupedResultParser();
            $resultData = $resultSet->getData();
            return $groupedResultParser->parse($resultData['grouped']);
        }

        return [new SolrSuggestResult($resultSet)];
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
