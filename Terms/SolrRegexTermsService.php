<?php

namespace Markup\NeedleBundle\Terms;

use Markup\NeedleBundle\Query\SimpleQueryInterface;
use Solarium\Exception\ExceptionInterface as SolariumException;

/**
 * A terms service using Solr/Solarium.
 */
class SolrRegexTermsService extends AbstractSolrTermsService implements TermsServiceInterface
{
    /**
     * @param SimpleQueryInterface $query
     *
     * @return TermsResultInterface
     */
    public function fetchTerms(SimpleQueryInterface $query)
    {
        $suggestQuery = $this->solarium->createTerms();
        $field = $this->fieldProvider->getField();

        if (!$field) {
            return new EmptyTermsResult();
        }

        $suggestQuery->setFields($field);
        $suggestQuery->setRegex(sprintf('.*%s.*', preg_quote((is_string($query->getSearchTerm())) ? $query->getSearchTerm() : '')));
        $suggestQuery->setSort('count');

        try {
            $resultSet = $this->solarium->terms($suggestQuery);
        } catch (SolariumException $e) {
            $this->logger->critical(
                'A suggest query did not complete successfully. Have you enabled the terms Solr component?',
                ['message' => ($e instanceof \Exception) ? $e->getMessage() : '(not available)']
            );

            return new EmptyTermsResult();
        }

        return new SolrTermsResult($resultSet);
    }
}
