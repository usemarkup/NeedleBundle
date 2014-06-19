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
        $this->solariumClient = $this->getMock('Solarium\Client');
        $this->subjectMapperProvider = $this->getMockBuilder('Markup\NeedleBundle\Indexer\SubjectDataMapperProvider')
            ->disableOriginalConstructor()
            ->getMock();
        $this->subjectToDataMapper = $this->getMock('Markup\NeedleBundle\Indexer\SubjectDataMapperInterface');
        $this->subjectMapperProvider
            ->expects($this->any())
            ->method('fetchMapperForCorpus')
            ->will($this->returnValue($this->subjectToDataMapper));
        $this->filterQueryLucenifier = $this->getMockBuilder('Markup\NeedleBundle\Lucene\FilterQueryLucenifier')
            ->disableOriginalConstructor()
            ->getMock();
        $this->eventDispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->indexCallbackProvider = $this->getMockBuilder('Markup\NeedleBundle\Indexer\IndexCallbackProvider')
            ->disableOriginalConstructor()
            ->getMock();
        $this->shouldReplaceDocuments = true;
        $this->logger = $this->getMock('Psr\Log\LoggerInterface');
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
        $corpus = $this->getMock('Markup\NeedleBundle\Corpus\CorpusInterface');
        $corpus
            ->expects($this->once())
            ->method('getSubjectIteration')
            ->will($this->returnValue(new \ArrayIterator(array($subject, $subject, $subject))));
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
            ->will($this->returnValue(array()));
        $this->command->setCorpusName($corpusName);
        call_user_func($this->command);
    }

    public function testExecuteWithoutCorpusNameThrowsBadMethodCall()
    {
        $this->setExpectedException('BadMethodCallException');
        call_user_func($this->command);
    }
}
