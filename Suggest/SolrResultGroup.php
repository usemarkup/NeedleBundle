<?php

namespace Markup\NeedleBundle\Suggest;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Solarium\QueryType\Suggester\Result\Term;

class SolrResultGroup implements ResultGroupInterface
{
    /**
     * @var string
     */
    private $term;

    /**
     * @var Term|array
     */
    private $data;

    /**
     * @param string     $term
     * @param Term|array $solariumTerm
     */
    public function __construct($term, $data)
    {
        $this->term = $term;
        $this->data = $data;
    }

    /**
     * Gets the keyword term for the group.
     *
     * @return string
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * @return array|Collection
     */
    public function getDocuments()
    {
        if ($this->data instanceof Term) {
            return [new ArrayCollection($this->data->getSuggestions())];
        }
        if (!array_key_exists('docs', $this->data) || !is_array($this->data['docs'])) {
            return [];
        }
        $documents = [];
        foreach ($this->data['docs'] as $doc) {
            $documents[] = new ArrayCollection($doc);
        }

        return $documents;
    }

    /**
     * @return int The custom count as an integer.
     */
    public function count()
    {
        if ($this->data instanceof Term) {
            return $this->data->getNumFound();
        }

        return (array_key_exists('numFound', $this->data)) ? intval($this->data['numFound']) : 0;
    }
}
