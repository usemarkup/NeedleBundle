<?php

namespace Markup\NeedleBundle\Result;

use Markup\NeedleBundle\Query\SimpleQueryInterface;
use Markup\NeedleBundle\Spellcheck\SolariumSpellcheckResult;
use Markup\NeedleBundle\Spellcheck\SpellcheckResultInterface;
use Markup\NeedleBundle\Spellcheck\SpellcheckResultStrategyInterface;
use Solarium\QueryType\Select\Result\Result as SolariumResult;
use Solarium\QueryType\Select\Result\Spellcheck\Result as SpellcheckResult;

class SolariumSpellcheckResultStrategy implements SpellcheckResultStrategyInterface
{
    /**
     * @var SolariumResult
     **/
    private $solariumResult;

    /**
     * @var SimpleQueryInterface
     */
    private $query;

    /**
     * A closure that returns a Solarium result object.
     *
     * @var \Closure
     **/
    private $solariumResultClosure;

    /**
     * @param SolariumResult|\Closure $result
     * @param SimpleQueryInterface    $query
     */
    public function __construct($result, SimpleQueryInterface $query)
    {
        if ($result instanceof SolariumResult) {
            $this->solariumResult = $result;
        } elseif ($result instanceof \Closure) {
            $this->solariumResultClosure = $result;
        } else {
            throw new \InvalidArgumentException(sprintf('Passed an instance of %s as a result into %s. Expected a Solarium result instance (Solarium\QueryType\Select\Result\Result) or a closure that returns a Solarium result instance.', get_class($result), __METHOD__));
        }
        $this->query = $query;
    }

    /**
     * @return SpellcheckResultInterface|null
     */
    public function getSpellcheckResult()
    {
        $solariumResult = $this->getSolariumResult();
        /** @var SpellcheckResult|null $spellcheck */
        $spellcheck = $solariumResult->getSpellcheck();
        if (!$spellcheck) {
            return null;
        }

        return new SolariumSpellcheckResult($solariumResult->getSpellcheck(), $this->query);
    }

    /**
     * @return SolariumResult
     **/
    private function getSolariumResult()
    {
        if (null === $this->solariumResult) {
            $this->solariumResult = $this->solariumResultClosure->__invoke();
        }

        return $this->solariumResult;
    }
}
