<?php

namespace Markup\NeedleBundle\Provider;

/**
* A null pattern collator provider.
*/
class NullCollatorProvider implements CollatorProviderInterface
{
    /**
     * {@inheritdoc}
     **/
    public function getCollatorForKey($key)
    {
        return null;
    }
}
