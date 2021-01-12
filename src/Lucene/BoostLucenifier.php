<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Lucene;

use Markup\NeedleBundle\Boost\BoostQueryFieldInterface;

/**
 * An object that can return Lucene syntax for a boost query field.
 */
class BoostLucenifier
{
    public function lucenifyBoost(BoostQueryFieldInterface $boostQueryField): string
    {
        return sprintf(
            '%s%s',
            $boostQueryField->getAttribute()->getSearchKey(['prefer_parsed' => true]),
            ($boostQueryField->getBoostFactor() != 1) ? ('^'.strval($boostQueryField->getBoostFactor())) : ''
        );
    }
}
