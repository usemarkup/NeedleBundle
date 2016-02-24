<?php

namespace Markup\NeedleBundle\Terms;

use Markup\NeedleBundle\Query\SimpleQueryInterface;
use Solarium\Exception\ExceptionInterface as SolariumException;

/**
 * A terms service using Solr/Solarium.
 */
class SolrPrefixTermsService extends AbstractSolrTermsService implements TermsServiceInterface
{
    /**
     * @param SimpleQueryInterface $query
     *
     * @return TermsResultInterface[]
     */
    public function fetchTerms(SimpleQueryInterface $query)
    {
        $suggestQuery = $this->solarium->createTerms();
        $field = $this->fieldProvider->getField();

        if (!$field) {
            return new EmptyTermsResult();
        }

        $suggestQuery
            ->setFields($field)
            ->setPrefix($query->getSearchTerm())
            ->setSort('count');

        try {
            $resultSet = $this->solarium->terms($suggestQuery);
        } catch (SolariumException $e) {
            $this->logger->critical(
                'A suggest query did not complete successfully. Have you enabled the terms Solr component?'
            );

            return new EmptyTermsResult();
        }

        return new SolrTermsResult($resultSet);
    }
}
