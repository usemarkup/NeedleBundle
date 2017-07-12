<?php

namespace Markup\NeedleBundle\Query;

use function GuzzleHttp\Promise\promise_for;

trait FallbackAsyncQueryTrait
{
    public function getResultAsync()
    {
        return promise_for($this->getResult());
    }
}
