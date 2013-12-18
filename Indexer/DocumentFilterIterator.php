<?php

namespace Markup\NeedleBundle\Indexer;

use Solarium\QueryType\Update\Query\Document\Document;

/**
 * A filter iterator to filter out everything except Solarium update documents.
 */
class DocumentFilterIterator extends \FilterIterator
{
    public function accept()
    {
        return $this->getInnerIterator()->current() instanceof Document;
    }
}
