<?php

namespace Markup\NeedleBundle\Provider;

/**
 * An interface for a provider object that can provide a collator to use for a specific key.
 **/
interface CollatorProviderInterface
{
    /**
     * Gets a collator to use for a given key.  May return null if there is no specific collator to provide.
     *
     * @param string $key
     *
     * @return \Markup\NeedleBundle\Collator\CollatorInterface|null
     **/
    public function getCollatorForKey($key);
}
