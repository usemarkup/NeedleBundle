<?php

namespace Markup\NeedleBundle\Spellcheck;

interface DictionaryProviderInterface
{
    /**
     * @return string
     */
    public function getDictionary();
}
