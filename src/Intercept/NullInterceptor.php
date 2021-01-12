<?php

namespace Markup\NeedleBundle\Intercept;

/**
* A null interceptor object.
*/
class NullInterceptor implements InterceptorInterface
{
    /**
     * {@inheritdoc}
     **/
    public function matchQueryToIntercept($queryString)
    {
        return null;
    }
}
