<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Result;

use Pagerfanta\Pagerfanta;

/**
 * An interface to signal on a result that an underlying Pagerfanta object _may_ be exposed (and can be requested).
 */
interface CanExposePagerfantaInterface
{
    public function getPagerfanta(): ?Pagerfanta;
}
