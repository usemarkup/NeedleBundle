<?php

namespace Markup\NeedleBundle\Terms;

use Solarium\QueryType\Terms\Result as SolariumResult;
use Traversable;

/**
 * A terms result wrapping a result from Solr/Solarium.
 */
class SolrTermsResult implements TermsResultInterface
{
    /**
     * @var SolariumResult
     */
    private $data;

    /**
     * @param SolariumResult $data
     */
    public function __construct(SolariumResult $data)
    {
        $this->data = $data;
    }

    /**
     * Retrieve an external iterator
     * @return Traversable
     */
    public function getIterator()
    {
        return $this->data->getIterator();
    }

    /**
     * @return array
     */
    public function getFields()
    {
        $options = $this->data->getQuery()->getOptions();

        return isset($options['fields']) ? $options['fields'] : [];
    }
}
