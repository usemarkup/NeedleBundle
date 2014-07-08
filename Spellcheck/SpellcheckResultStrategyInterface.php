<?php

namespace Markup\NeedleBundle\Spellcheck;

interface SpellcheckResultStrategyInterface
{
    /**
     * @return SpellcheckResultInterface|null
     */
    public function getSpellcheckResult();
} 
