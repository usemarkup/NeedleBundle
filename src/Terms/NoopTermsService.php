<?php

namespace Markup\NeedleBundle\Terms;

use Markup\NeedleBundle\Query\SimpleQueryInterface;

class NoopTermsService implements TermsServiceInterface
{
    /**
     * @param SimpleQueryInterface $query
     *
     * @return TermsResultInterface
     */
    public function fetchTerms(SimpleQueryInterface $query)
    {
        return new EmptyTermsResult();
    }
}
