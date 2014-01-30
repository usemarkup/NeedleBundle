<?php

namespace Markup\NeedleBundle\Tests\Indexer;

use Markup\NeedleBundle\Exception\IllegalSubjectException;
use Markup\NeedleBundle\Indexer\SubjectDocumentGenerator;
use Markup\NeedleBundle\Indexer\SubjectDocumentGeneratorInterface;
use Mockery as m;
use Solarium\QueryType\Update\Query\Document\Document;

/**
* A test for an object that can generate a Solarium document for a subject.
*/
class SubjectDocumentGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->updateQuery = m::mock('Solarium\QueryType\Update\Query\Query');
        $this->subjectDataMapper = m::mock('Markup\NeedleBundle\Indexer\SubjectDataMapperInterface')->shouldIgnoreMissing();
        $this->generator = new SubjectDocumentGenerator($this->subjectDataMapper);
        $this->generator->setUpdateQuery($this->updateQuery);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testIsSubjectDocumentGenerator()
    {
        $this->assertTrue($this->generator instanceof SubjectDocumentGeneratorInterface);
    }

    public function testCreateHasOneCreateDocumentCall()
    {
        $subject = new \stdClass();
        $document = m::mock('Solarium\QueryType\Update\Query\Document\Document');
        $this->updateQuery
            ->shouldReceive('createDocument')
            ->once()
            ->andReturn($document);
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
            ->shouldReceive('mapSubjectToData')
            ->with($subject)
            ->andThrow(new IllegalSubjectException());
        $this->updateQuery
            ->shouldReceive('createDocument')
            ->never();
        $this->assertNull($this->generator->createDocumentForSubject($subject));
    }

    public function testCreateDocumentNotAllowingNulls()
    {
        $data = array(
            'id' => 1,
            'field' => null,
        );
        $this->updateQuery
            ->shouldReceive('createDocument')
            ->andReturnUsing(function ($data) {
                return new Document($data);
            });
        $this->subjectDataMapper
            ->shouldReceive('mapSubjectToData')
            ->andReturn($data);
        $generator = new SubjectDocumentGenerator($this->subjectDataMapper, $allowNullValues = false);
        $generator->setUpdateQuery($this->updateQuery);
        $document = $generator->createDocumentForSubject([]);
        $this->assertInstanceOf('Solarium\QueryType\Update\Query\Document\Document', $document);
        $this->assertCount(1, $document->getFields());
    }
}
