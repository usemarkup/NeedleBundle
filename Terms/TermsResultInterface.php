<?php

namespace Markup\NeedleBundle\Terms;

/**
 * An interface for a terms result.
 */
interface TermsResultInterface extends \IteratorAggregate
{
    /**
     * @return array
     */
    public function getFields();
}