<?php

namespace Markup\NeedleBundle\Terms;

class NullTermsFieldProvider implements TermsFieldProviderInterface
{
    /**
     * @return string|null
     */
    public function getField()
    {
        return null;
    }
}
