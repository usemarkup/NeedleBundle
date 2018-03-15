<?php

namespace Markup\NeedleBundle\Terms;

use Markup\NeedleBundle\Query\SimpleQueryInterface;

/**
 * An interface for a service that can make a lookup for terms.
 */
interface TermsServiceInterface
{
    /**
     * @param SimpleQueryInterface $query
     *
     * @return TermsResultInterface[]
     */
    public function fetchTerms(SimpleQueryInterface $query);
}
