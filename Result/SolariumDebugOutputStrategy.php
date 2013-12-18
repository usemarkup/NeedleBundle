<?php

namespace Markup\NeedleBundle\Result;

use Symfony\Component\Templating\EngineInterface as TemplatingEngine;
use Solarium\QueryType\Select\Result\Result as SolariumResult;

/**
* A debug output strategy for Solarium results.
*/
class SolariumDebugOutputStrategy implements DebugOutputStrategyInterface
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
     * @var TemplatingEngine
     **/
    private $templating;

    /**
     * @param SolariumResult|\Closure $result
     * @param TemplatingEngine        $templating
     **/
    public function __construct($result, TemplatingEngine $templating)
    {
        if ($result instanceof SolariumResult) {
            $this->solariumResult = $result;
        } elseif ($result instanceof \Closure) {
            $this->solariumResultClosure = $result;
        } else {
            throw new \InvalidArgumentException(sprintf('Passed an instance of %s as a result into %s. Expected a Solarium result instance (Solarium\QueryType\Select\Result\Result) or a closure that returns a Solarium result instance.', get_class($result), __METHOD__));
        }
        $this->templating = $templating;
    }

    public function hasDebugOutput()
    {
        return null !== $this->getSolariumResult()->getDebug();
    }

    public function getDebugOutput()
    {
        if ($this->hasDebugOutput()) {
            return $this->templating->render(
                'MarkupNeedleBundle:Solarium:debug_output.html.twig',
                array(
                    'debug'         => $this->getSolariumResult()->getDebug(),
                    )
                );
        }
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
