<?php

namespace Markup\NeedleBundle\Result;

use Markup\NeedleBundle\Spellcheck\SpellcheckResultInterface;
use Markup\NeedleBundle\Spellcheck\SpellcheckResultStrategyInterface;
use Pagerfanta\Pagerfanta;

/**
* A result adapter that wraps a Pagerfanta instance.
*/
class PagerfantaResultAdapter implements ResultInterface
{
    /**
     * A Pagerfanta instance.
     *
     * @var Pagerfanta
     **/
    private $pagerfanta;

    /**
     * A strategy that can fetch the query time for a result.
     *
     * @var QueryTimeStrategyInterface
     **/
    private $queryTimeStrategy;

    /**
     * A strategy that can fetch the facet sets for a result.
     *
     * @var FacetSetStrategyInterface
     **/
    private $facetSetStrategy;

    /**
     * A spellcheck result.
     *
     * @var SpellcheckResultStrategyInterface
     */
    private $spellcheckResultStrategy;

    /**
     * A strategy that can fetch debug info for a result.
     *
     * @var DebugOutputStrategyInterface
     **/
    private $debugOutputStrategy;

    /**
     * @param Pagerfanta $pagerfanta
     **/
    public function __construct(Pagerfanta $pagerfanta)
    {
        $this->pagerfanta = $pagerfanta;
    }

    public function getTotalCount()
    {
        return $this->getPagerfanta()->getNbResults();
    }

    public function getIterator()
    {
        return $this->getPagerfanta()->getIterator();
    }

    public function count()
    {
        return $this->getTotalCount();
    }

    public function setQueryTimeStrategy(QueryTimeStrategyInterface $queryTimeStrategy)
    {
        $this->queryTimeStrategy = $queryTimeStrategy;
    }

    public function getQueryTimeInMilliseconds()
    {
        //if there's no query time strategy, return false
        if (null === $this->queryTimeStrategy) {
            return false;
        }

        return $this->queryTimeStrategy->getQueryTimeInMilliseconds();
    }

    public function getTotalPageCount()
    {
        return $this->getPagerfanta()->getNbPages();
    }

    public function getCurrentPageNumber()
    {
        return $this->getPagerfanta()->getCurrentPage();
    }

    public function isPaginated()
    {
        return $this->getPagerfanta()->haveToPaginate();
    }

    public function hasPreviousPage()
    {
        return $this->getPagerfanta()->hasPreviousPage();
    }

    public function getPreviousPageNumber()
    {
        try {
            $page = $this->getPagerfanta()->getPreviousPage();
        } catch (\LogicException $e) {
            //if there is a \LogicException thrown here we'll make assumption it is because page doesn't exist
            throw new PageDoesNotExistException(sprintf('Tried to get number for non-existent page. Original exception message: "%s"', $e->getMessage()));
        }

        return $page;
    }

    public function hasNextPage()
    {
        return $this->getPagerfanta()->hasNextPage();
    }

    public function getNextPageNumber()
    {
        try {
            $page = $this->getPagerfanta()->getNextPage();
        } catch (\LogicException $e) {
            //if there is a \LogicException thrown here we'll make assumption it is because page doesn't exist
            throw new PageDoesNotExistException(sprintf('Tried to get number for non-existent page. Original exception message: "%s"', $e->getMessage()));
        }

        return $page;
    }

    public function setFacetSetStrategy(FacetSetStrategyInterface $facetSetStrategy)
    {
        $this->facetSetStrategy = $facetSetStrategy;
    }

    public function getFacetSets()
    {
        if (null === $this->facetSetStrategy) {
            return [];
        }

        return $this->facetSetStrategy->getFacetSets();
    }

    /**
     * @param SpellcheckResultInterface $spellcheckResult
     */
    public function setSpellcheckResultStrategy(SpellcheckResultStrategyInterface $spellcheckResultStrategy)
    {
        $this->spellcheckResultStrategy = $spellcheckResultStrategy;

        return $this;
    }

    /**
     * @return SpellcheckResultInterface|null
     */
    public function getSpellcheckResult()
    {
        return $this->spellcheckResultStrategy->getSpellcheckResult();
    }

    public function setDebugOutputStrategy(DebugOutputStrategyInterface $debugOutputStrategy)
    {
        $this->debugOutputStrategy = $debugOutputStrategy;
    }

    public function hasDebugOutput()
    {
        if (null === $this->debugOutputStrategy) {
            return false;
        }

        return $this->debugOutputStrategy->hasDebugOutput();
    }

    public function getDebugOutput()
    {
        if (null !== $this->debugOutputStrategy and $this->debugOutputStrategy->hasDebugOutput()) {
            return $this->debugOutputStrategy->getDebugOutput();
        }
    }

    /**
     * Gets the Pagerfanta.
     *
     * @return Pagerfanta
     **/
    public function getPagerfanta()
    {
        return $this->pagerfanta;
    }
}
