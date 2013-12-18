<?php

namespace Markup\NeedleBundle\Query;

/**
 * An interface for a select query that can record itself internally.
 **/
interface RecordableSelectQueryInterface extends SelectQueryInterface
{
    /**
     * Makes this query save a record of itself internally.
     **/
    public function record();

    /**
     * Gets whether this query contains an internal record of itself.
     *
     * @return bool
     **/
    public function hasRecord();

    /**
     * Gets this query's internal record of itself.  Returns null if there is no internal record.
     *
     * @return self
     **/
    public function getRecord();
}
