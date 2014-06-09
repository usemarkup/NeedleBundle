<?php

namespace Markup\NeedleBundle\Suggest;

use Doctrine\Common\Collections\ArrayCollection;
use Solarium\QueryType\Suggester\Result\Term;

class SolrResultGroup implements ResultGroupInterface
{
    /**
     * @var string
     */
    private $term;

    /**
     * @var Term
     */
    private $solariumTerm;

    /**
     * @param string $term
     * @param Term   $solariumTerm
     */
    public function __construct($term, Term $solariumTerm)
    {
        $this->term = $term;
        $this->solariumTerm = $solariumTerm;
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
     * @return array<Collection>
     */
    public function getDocuments()
    {
        return array(new ArrayCollection($this->solariumTerm->getSuggestions()));
    }

    /**
     * @return int The custom count as an integer.
     */
    public function count()
    {
        return $this->solariumTerm->getNumFound();
    }
}
