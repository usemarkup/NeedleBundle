<?php

namespace Markup\NeedleBundle\Terms;

class EmptyTermsResult implements TermsResultInterface
{
    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator([]);
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return [];
    }
}
