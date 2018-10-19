<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Tests\Client;

use Markup\NeedleBundle\Client\BackendClientServiceLocator;
use PHPUnit\Framework\TestCase;

class BackendClientServiceLocatorTest extends TestCase
{
    /**
     * @var \stdClass
     */
    private $service;

    /**
     * @var BackendClientServiceLocator
     */
    private $locator;

    protected function setUp()
    {
        $this->service = new \stdClass();
        $this->locator = new BackendClientServiceLocator([
            'corpus' => function () {
                return $this->service;
            },
        ]);
    }

    public function testGetClientForCorpus()
    {
        $this->assertSame($this->service, $this->locator->fetchClientForCorpus('corpus'));
    }
}
