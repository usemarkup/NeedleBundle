<?php

namespace Markup\NeedleBundle\Tests\Indexer;

use Markup\NeedleBundle\Corpus\CorpusInterface;
use Markup\NeedleBundle\Corpus\CorpusProvider;
use Markup\NeedleBundle\Indexer\CorpusIndexingCommand;
use Markup\NeedleBundle\Indexer\IndexingMessagerInterface;
use Markup\NeedleBundle\Indexer\IndexingMessagerLocator;
use Markup\NeedleBundle\Indexer\IndexingResultInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
* A test for an indexing command that indexes a corpus.
*/
class CorpusIndexingCommandTest extends MockeryTestCase
{
    /**
     * @var CorpusProvider|m\MockInterface
     */
    private $corpusProvider;

    /**
     * @var ContainerInterface|m\MockInterface
     */
    private $messagerLocator;

    /**
     * @var EventDispatcherInterface|m\MockInterface
     */
    private $eventDispatcher;

    /**
     * @var bool
     */
    private $shouldReplaceDocuments;

    /**
     * @var CorpusIndexingCommand
     */
    private $command;

    protected function setUp()
    {
        $this->corpusProvider = m::mock(CorpusProvider::class);
        $this->messagerLocator = m::mock(IndexingMessagerLocator::class);
        $this->eventDispatcher = m::spy(EventDispatcherInterface::class);
        $this->shouldReplaceDocuments = true;
        $this->command = new CorpusIndexingCommand(
            $this->corpusProvider,
            $this->messagerLocator,
            $this->eventDispatcher
        );
    }

    public function testGetAllSubjectsFromService()
    {
        $subject = new \stdClass();
        $corpus = m::mock(CorpusInterface::class);
        $corpus
            ->shouldReceive('getSubjectIteration')
            ->once()
            ->andReturn(new \ArrayIterator([$subject, $subject, $subject]));
        $corpusName = 'corpus';
        $corpus
            ->shouldReceive('getName')
            ->andReturn($corpusName);
        $this->corpusProvider
            ->shouldReceive('fetchNamedCorpus')
            ->with($corpusName)
            ->andReturn($corpus);
        $messager = m::spy(IndexingMessagerInterface::class)
            ->shouldReceive('executeIndex')
            ->andReturn(m::spy(IndexingResultInterface::class))
            ->getMock();
        $this->messagerLocator
            ->shouldReceive('get')
            ->with($corpusName)
            ->andReturn($messager);

        ($this->command)($corpusName);

        $messager->shouldHaveReceived('executeIndex');
    }
}
