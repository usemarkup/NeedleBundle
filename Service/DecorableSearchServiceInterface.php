<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Service;

use Markup\NeedleBundle\Query\ResolvedSelectQueryDecoratorInterface;

/**
 * An interface for search services indicating they can be decorated.
 */
interface DecorableSearchServiceInterface
{
    public function addDecorator(ResolvedSelectQueryDecoratorInterface $decorator): void;
}
