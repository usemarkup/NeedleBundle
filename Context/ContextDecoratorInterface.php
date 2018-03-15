<?php

namespace Markup\NeedleBundle\Context;

/**
 * Interface for a class that can decorate a search context.
 */
interface ContextDecoratorInterface
{
    /**
     * Decorates a provided search context.
     *
     * @param SearchContextInterface $context
     * @return SearchContextInterface
     */
    public function decorateContext(SearchContextInterface $context);
}
