<?php

namespace Markup\NeedleBundle\Sort;

/**
* A empty sort collection.
*/
class EmptySortCollection extends SortCollection
{
    public function __construct()
    {
        parent::__construct([]);
    }
}
