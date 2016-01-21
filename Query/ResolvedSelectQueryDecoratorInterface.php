<?php

namespace Markup\NeedleBundle\Query;

/**
 * Decorates a ResolvedSelectQuery
 */
interface ResolvedSelectQueryDecoratorInterface
{
    /**
     * @param  ResolvedSelectQueryInterface $query
     * @return ResolvedSelectQueryInterface
     */
    public function decorate(ResolvedSelectQueryInterface $query);
}
