<?php

namespace Markup\NeedleBundle\Lucene;

use Solarium\Core\Query\Helper as SolariumHelper;

/**
* A Lucene helper.
*/
class Helper implements HelperInterface
{
    /**
     * @var SolariumHelper
     **/
    private $solariumHelper;

    /**
     * @param SolariumHelper $solariumHelper
     **/
    public function __construct(SolariumHelper $solariumHelper)
    {
        $this->solariumHelper = $solariumHelper;
    }

    /**
     * {@inheritdoc}
     **/
    public function assemble($query, $parts)
    {
        return $this->solariumHelper->assemble($query, $parts);
    }
}
