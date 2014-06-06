<?php

namespace Markup\NeedleBundle\Result;

/**
 * Interface for a result from an update query.
 */
interface UpdateResultInterface
{
    /**
     * Gets whether the update was successful.
     *
     * @return bool
     */
    public function isSuccessful();

    /**
     * Gets whichever status code it is that the backend emits.
     *
     * @return mixed
     */
    public function getStatusCode();

    /**
     * @return float|int
     */
    public function getQueryTimeInMilliseconds();
} 
