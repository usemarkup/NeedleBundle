<?php

namespace Markup\NeedleBundle\Result;

use Markup\NeedleBundle\Spellcheck\SpellcheckResultInterface;
use Markup\NeedleBundle\Spellcheck\SpellcheckResultStrategyInterface;
use Pagerfanta\Pagerfanta;

/**
* A result adapter that wraps a Pagerfanta instance.
*/
class PagerfantaResultAdapter implements ResultInterface, CanExposePagerfantaInterface
{
    /**
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
     * @var array
     */
    private $mappingHashForFields;

    public function __construct(Pagerfanta $pagerfanta)
    {
        $this->pagerfanta = $pagerfanta;
        $this->mappingHashForFields = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        return $this->getPagerfanta()->getNbResults();
    }

    public function getIterator()
    {
        return $this->getPagerfanta()->getIterator();
    }

    public function count()
    {
        return $this->getNbResults();
    }

    public function setQueryTimeStrategy(QueryTimeStrategyInterface $queryTimeStrategy)
    {
        $this->queryTimeStrategy = $queryTimeStrategy;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryTimeInMilliseconds()
    {
        if (null === $this->queryTimeStrategy) {
            return 0.0;
        }

        return $this->queryTimeStrategy->getQueryTimeInMilliseconds();
    }

    /**
     * {@inheritdoc}
     */
    public function getNbPages()
    {
        return $this->getPagerfanta()->getNbPages();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentPage()
    {
        return $this->getPagerfanta()->getCurrentPage();
    }

    /**
     * {@inheritdoc}
     */
    public function haveToPaginate()
    {
        return $this->getPagerfanta()->haveToPaginate();
    }

    /**
     * {@inheritdoc}
     */
    public function hasPreviousPage()
    {
        return $this->getPagerfanta()->hasPreviousPage();
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviousPage()
    {
        try {
            $page = $this->getPagerfanta()->getPreviousPage();
        } catch (\LogicException $e) {
            //if there is a \LogicException thrown here we'll make assumption it is because page doesn't exist
            throw new PageDoesNotExistException(sprintf('Tried to get number for non-existent page. Original exception message: "%s"', $e->getMessage()));
        }

        return $page;
    }

    /**
     * {@inheritdoc}
     */
    public function hasNextPage()
    {
        return $this->getPagerfanta()->hasNextPage();
    }

    /**
     * {@inheritdoc}
     */
    public function getNextPage()
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

    /**
     * {@inheritdoc}
     */
    public function getFacetSets()
    {
        if (null === $this->facetSetStrategy) {
            return [];
        }

        return $this->facetSetStrategy->getFacetSets();
    }

    public function setSpellcheckResultStrategy(SpellcheckResultStrategyInterface $spellcheckResultStrategy)
    {
        $this->spellcheckResultStrategy = $spellcheckResultStrategy;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSpellcheckResult()
    {
        return $this->spellcheckResultStrategy->getSpellcheckResult();
    }

    public function setDebugOutputStrategy(DebugOutputStrategyInterface $debugOutputStrategy)
    {
        $this->debugOutputStrategy = $debugOutputStrategy;
    }

    /**
     * {@inheritdoc}
     */
    public function hasDebugOutput()
    {
        if (null === $this->debugOutputStrategy) {
            return false;
        }

        return $this->debugOutputStrategy->hasDebugOutput();
    }

    /**
     * {@inheritdoc}
     */
    public function getDebugOutput()
    {
        if (null !== $this->debugOutputStrategy and $this->debugOutputStrategy->hasDebugOutput()) {
            return $this->debugOutputStrategy->getDebugOutput();
        }
    }

    /**
     * Gets the Pagerfanta.
     **/
    public function getPagerfanta(): Pagerfanta
    {
        return $this->pagerfanta;
    }

    public function setMappingHashForFields(array $hash)
    {
        $this->mappingHashForFields = $hash;
    }

    public function getMappingHashForFields(): array
    {
        return $this->mappingHashForFields;
    }

    public function getMaxPerPage(): int
    {
        return $this->pagerfanta->getMaxPerPage();
    }
}
