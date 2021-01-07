<?php

namespace Markup\NeedleBundle\Suggest;

use Doctrine\Common\Collections\Collection;

interface ResultGroupInterface extends \Countable
{
    /**
     * Gets the keyword term for the group.
     *
     * @return string
     */
    public function getTerm();

    /**
     * @return array|Collection
     */
    public function getDocuments();
}
