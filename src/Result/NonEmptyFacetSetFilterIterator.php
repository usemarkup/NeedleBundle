<?php

namespace Markup\NeedleBundle\Result;

/**
* A filter iterator that filters members of an iteration down to ones that are non-empty.
*/
class NonEmptyFacetSetFilterIterator extends \FilterIterator
{
    public function accept()
    {
        $current = $this->getInnerIterator()->current();

        return count($current) > 0;
    }
}
