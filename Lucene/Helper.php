<?php

namespace Markup\NeedleBundle\Lucene;

use Markup\NeedleBundle\Exception\LuceneSyntaxException;
use Solarium\Core\Query\Helper as SolariumHelper;
use Solarium\Exception\ExceptionInterface as SolariumException;

/**
* A Lucene helper.
*/
class Helper implements HelperInterface
{
    /**
     * @var SolariumHelper
     **/
    private $solariumHelper;

    public function __construct(?SolariumHelper $solariumHelper = null)
    {
        $this->solariumHelper = $solariumHelper ?? new SolariumHelper();
    }

    /**
     * {@inheritdoc}
     **/
    public function assemble($query, $parts)
    {
        try {
            $assembled = $this->solariumHelper->assemble($query, $parts);
        } catch (SolariumException $e) {
            throw new LuceneSyntaxException(
                sprintf(
                    'Could not build Lucene syntax with query "%s" and parts: %s. Underlying exception message: %s',
                    $query,
                    implode(', ', $parts),
                    ($e instanceof \Exception) ? $e->getMessage() : '(not available)'
                ),
                0,
                $e
            );
        }

        return $assembled;
    }
}
