<?php

namespace Markup\NeedleBundle\Tests\Indexer;

use Markup\NeedleBundle\Corpus\CorpusInterface;
use Markup\NeedleBundle\Corpus\CorpusProvider;
use Markup\NeedleBundle\Indexer\CorpusIndexingCommand;
use Markup\NeedleBundle\Indexer\IndexCallbackProvider;
use Markup\NeedleBundle\Indexer\SubjectDataMapperInterface;
use Markup\NeedleBundle\Indexer\SubjectDataMapperProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Solarium\Client;
use Solarium\QueryType\Update\Query\Query;
use Solarium\QueryType\Update\Result;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
* A test for an indexing command that indexes a corpus.
*/
class CorpusIndexingCommandTest extends TestCase
{
    protected function setUp()
    {
        $this->corpusProvider = $this->createMock(CorpusProvider::class);
        $this->solariumClient = $this->createMock(Client::class);
        $this->subjectMapperProvider = $this->createMock(SubjectDataMapperProvider::class);
        $this->subjectToDataMapper = $this->createMock(SubjectDataMapperInterface::class);
        $this->subjectMapperProvider
            ->expects($this->any())
            ->method('fetchMapperForCorpus')
            ->will($this->returnValue($this->subjectToDataMapper));
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->indexCallbackProvider = $this->createMock(IndexCallbackProvider::class);
        $this->shouldReplaceDocuments = true;
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->command = new CorpusIndexingCommand(
            $this->corpusProvider,
            $this->solariumClient,
            $this->subjectMapperProvider,
            $this->eventDispatcher,
            $this->indexCallbackProvider,
            $this->shouldReplaceDocuments,
            $this->logger
        );
    }

    public function testGetAllSubjectsFromService()
    {
        $subject = new \stdClass();
        $corpus = $this->createMock(CorpusInterface::class);
        $corpus
            ->expects($this->once())
            ->method('getSubjectIteration')
            ->will($this->returnValue(new \ArrayIterator([$subject, $subject, $subject])));
        $corpusName = 'corpus';
        $this->corpusProvider
            ->expects($this->any())
            ->method('fetchNamedCorpus')
            ->with($this->equalTo($corpusName))
            ->will($this->returnValue($corpus));
        $updateQuery = $this->createMock(Query::class);
        $this->solariumClient
            ->expects($this->any())
            ->method('createUpdate')
            ->will($this->returnValue($updateQuery));
        $result = $this->createMock(Result::class);
        $this->solariumClient
            ->expects($this->any())
            ->method('update')
            ->with($this->equalTo($updateQuery))
            ->will($this->returnValue($result));
        $this->indexCallbackProvider
            ->expects($this->any())
            ->method('getCallbacksForCorpus')
            ->will($this->returnValue([]));
        $this->command->setCorpusName($corpusName);
        call_user_func($this->command);
    }

    public function testExecuteWithoutCorpusNameThrowsBadMethodCall()
    {
        $this->expectException(\BadMethodCallException::class);
        call_user_func($this->command);
    }
}
