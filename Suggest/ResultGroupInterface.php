<?php

namespace Markup\NeedleBundle\Suggest;

interface ResultGroupInterface extends \Countable
{
    /**
     * Gets the keyword term for the group.
     *
     * @return string
     */
    public function getTerm();

    /**
     * @return array<Collection>
     */
    public function getDocuments();
}
