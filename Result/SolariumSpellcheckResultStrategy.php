<?php

namespace Markup\NeedleBundle\Result;

use Markup\NeedleBundle\Spellcheck\SolariumSpellcheckResult;
use Markup\NeedleBundle\Spellcheck\SpellcheckResultInterface;
use Markup\NeedleBundle\Spellcheck\SpellcheckResultStrategyInterface;
use Solarium\QueryType\Select\Result\Result as SolariumResult;

class SolariumSpellcheckResultStrategy implements SpellcheckResultStrategyInterface
{
    /**
     * @var SolariumResult
     **/
    private $solariumResult;

    /**
     * A closure that returns a Solarium result object.
     *
     * @var \Closure
     **/
    private $solariumResultClosure;

    /**
     * @param SolariumResult|\Closure $result
     */
    public function __construct($result)
    {
        if ($result instanceof SolariumResult) {
            $this->solariumResult = $result;
        } elseif ($result instanceof \Closure) {
            $this->solariumResultClosure = $result;
        } else {
            throw new \InvalidArgumentException(sprintf('Passed an instance of %s as a result into %s. Expected a Solarium result instance (Solarium\QueryType\Select\Result\Result) or a closure that returns a Solarium result instance.', get_class($result), __METHOD__));
        }
    }

    /**
     * @return SpellcheckResultInterface|null
     */
    public function getSpellcheckResult()
    {
        $solariumResult = $this->getSolariumResult();
        if (!$solariumResult->getSpellcheck()) {
            return null;
        }

        return new SolariumSpellcheckResult($solariumResult->getSpellcheck());
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
