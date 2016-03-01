<?php

namespace Markup\NeedleBundle\Terms;

/**
 * Interface for an object that provides field from which to retrieve terms.
 */
interface TermsFieldProviderInterface
{
    /**
     * @return string|null
     */
    public function getField();
} 
