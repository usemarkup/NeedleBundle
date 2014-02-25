<?php

namespace Markup\NeedleBundle\Collator;

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
