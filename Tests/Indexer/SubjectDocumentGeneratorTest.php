<?php

namespace Markup\NeedleBundle\Tests\Indexer;

use Markup\NeedleBundle\Exception\IllegalSubjectException;
use Markup\NeedleBundle\Indexer\SubjectDocumentGenerator;
use Markup\NeedleBundle\Indexer\SubjectDocumentGeneratorInterface;

/**
* A test for an object that can generate a Solarium document for a subject.
*/
class SubjectDocumentGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->updateQuery = $this->getMockBuilder('Solarium\QueryType\Update\Query\Query')
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->subjectDataMapper = $this->getMock('Markup\NeedleBundle\Indexer\SubjectDataMapperInterface');
        $this->generator = new SubjectDocumentGenerator($this->subjectDataMapper);
        $this->generator->setUpdateQuery($this->updateQuery);
    }

    public function testIsSubjectDocumentGenerator()
    {
        $this->assertTrue($this->generator instanceof SubjectDocumentGeneratorInterface);
    }

    public function testCreateHasOneCreateDocumentCall()
    {
        $subject = new \stdClass();
        $document = $this->getMockBuilder('Solarium\QueryType\Update\Query\Document\Document')
            ->disableOriginalConstructor()
            ->getMock();
        $this->updateQuery
            ->expects($this->once())
            ->method('createDocument')
            ->will($this->returnValue($document));
        $this->generator->createDocumentForSubject($subject);
    }

    public function testCallCreateDocumentForSubjectWithoutUpdateQuerySetThrowsRuntimeException()
    {
        $this->setExpectedException('RuntimeException');
        $generator = new SubjectDocumentGenerator($this->subjectDataMapper);
        $subject = new \stdClass();
        $generator->createDocumentForSubject($subject);
    }

    public function testCreateDocumentForIllegalSubjectReturnsNull()
    {
        $subject = new \stdClass();
        $this->subjectDataMapper
            ->expects($this->any())
            ->method('mapSubjectToData')
            ->with($this->equalTo($subject))
            ->will($this->throwException(new IllegalSubjectException()));
        $this->updateQuery
            ->expects($this->never())
            ->method('createDocument');
        $this->assertNull($this->generator->createDocumentForSubject($subject));
    }
}
