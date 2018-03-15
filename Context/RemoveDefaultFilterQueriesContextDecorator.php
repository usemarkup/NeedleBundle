<?php

namespace Markup\NeedleBundle\Context;

/**
 * A search context decorator that has no default filter queries.
 */
class RemoveDefaultFilterQueriesContextDecorator implements SearchContextInterface
{
    use DecorateContextTrait;

    const LARGE_NUMBER = 10000;

    /**
     * @param SearchContextInterface $searchContext The search context being decorated.
     **/
    public function __construct(SearchContextInterface $searchContext)
    {
        $this->context = $searchContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultFilterQueries()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getItemsPerPage()
    {
        return self::LARGE_NUMBER;
    }
}
