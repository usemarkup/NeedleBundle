<?php

namespace Markup\NeedleBundle\Tests\Indexer;

use Markup\NeedleBundle\Indexer\CorpusIndexingCommand;

/**
* A test for an indexing command that indexes a corpus.
*/
class CorpusIndexingCommandTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->corpusProvider = $this->getMockBuilder('Markup\NeedleBundle\Corpus\CorpusProvider')
            ->disableOriginalConstructor()
            ->getMock();
        $this->solariumClient = $this->createMock('Solarium\Client');
        $this->subjectMapperProvider = $this->getMockBuilder('Markup\NeedleBundle\Indexer\SubjectDataMapperProvider')
            ->disableOriginalConstructor()
            ->getMock();
        $this->subjectToDataMapper = $this->createMock('Markup\NeedleBundle\Indexer\SubjectDataMapperInterface');
        $this->subjectMapperProvider
            ->expects($this->any())
            ->method('fetchMapperForCorpus')
            ->will($this->returnValue($this->subjectToDataMapper));
        $this->filterQueryLucenifier = $this->getMockBuilder('Markup\NeedleBundle\Lucene\FilterQueryLucenifier')
            ->disableOriginalConstructor()
            ->getMock();
        $this->eventDispatcher = $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->indexCallbackProvider = $this->getMockBuilder('Markup\NeedleBundle\Indexer\IndexCallbackProvider')
            ->disableOriginalConstructor()
            ->getMock();
        $this->shouldReplaceDocuments = true;
        $this->logger = $this->createMock('Psr\Log\LoggerInterface');
        $this->command = new CorpusIndexingCommand(
            $this->corpusProvider,
            $this->solariumClient,
            $this->subjectMapperProvider,
            $this->filterQueryLucenifier,
            $this->eventDispatcher,
            $this->indexCallbackProvider,
            $this->shouldReplaceDocuments,
            $this->logger
        );
    }

    public function testGetAllSubjectsFromService()
    {
        $subject = new \stdClass();
        $corpus = $this->createMock('Markup\NeedleBundle\Corpus\CorpusInterface');
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
        $updateQuery = $this->getMockBuilder('Solarium\QueryType\Update\Query\Query')
                ->disableOriginalConstructor()
                ->getMock();
        $this->solariumClient
            ->expects($this->any())
            ->method('createUpdate')
            ->will($this->returnValue($updateQuery));
        $result = $this->getMockBuilder('Solarium\QueryType\Update\Result')
                ->disableOriginalConstructor()
                ->getMock();
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
        $this->setExpectedException('BadMethodCallException');
        call_user_func($this->command);
    }
}
