<?php

namespace Markup\NeedleBundle\Suggest;

use Markup\NeedleBundle\Query\SimpleQueryInterface;
use Solarium\Client as Solarium;

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
     * @param Solarium $solarium
     */
    public function __construct(Solarium $solarium)
    {
        $this->solarium = $solarium;
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

        $resultSet = $this->solarium->suggester($suggestQuery);

        return new SolrSuggestResult($resultSet);
    }
}
