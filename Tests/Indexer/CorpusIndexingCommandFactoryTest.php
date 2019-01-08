<?php
declare(strict_types=1);

namespace Markup\NeedleBundle\Tests\Indexer;

use Markup\NeedleBundle\Indexer\CorpusIndexingCommand;
use Markup\NeedleBundle\Indexer\CorpusIndexingCommandFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class CorpusIndexingCommandFactoryTest extends MockeryTestCase
{
    /**
     * @var CorpusIndexingCommand|m\MockInterface
     */
    private $indexingCommand;

    /**
     * @var CorpusIndexingCommandFactory
     */
    private $factory;

    protected function setUp()
    {
        $this->indexingCommand = m::mock(CorpusIndexingCommand::class);
        $this->factory = new CorpusIndexingCommandFactory(function () {
            return $this->indexingCommand;
        });
    }

    public function testCreateProvidesCommand()
    {
        $this->assertSame($this->indexingCommand, $this->factory->create());
    }
}
