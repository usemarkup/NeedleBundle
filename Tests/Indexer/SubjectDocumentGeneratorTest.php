<?php

namespace Markup\NeedleBundle\Tests\Indexer;

use Markup\NeedleBundle\Exception\IllegalSubjectException;
use Markup\NeedleBundle\Indexer\SubjectDataMapperInterface;
use Markup\NeedleBundle\Indexer\SubjectDocumentGenerator;
use Markup\NeedleBundle\Indexer\SubjectDocumentGeneratorInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Solarium\QueryType\Update\Query\Document\Document;
use Solarium\QueryType\Update\Query\Query;

/**
* A test for an object that can generate a Solarium document for a subject.
*/
class SubjectDocumentGeneratorTest extends MockeryTestCase
{
    /**
     * @var Query|m\MockInterface
     */
    private $updateQuery;

    /**
     * @var SubjectDataMapperInterface|m\MockInterface
     */
    private $subjectDataMapper;

    /**
     * @var SubjectDocumentGenerator
     */
    private $generator;

    protected function setUp()
    {
        $this->updateQuery = m::mock(Query::class);
        $this->subjectDataMapper = m::mock(SubjectDataMapperInterface::class)->shouldIgnoreMissing();
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
        $document = m::mock('Solarium\QueryType\Update\Query\Document\Document');
        $this->updateQuery
            ->shouldReceive('createDocument')
            ->once()
            ->andReturn($document);
        $this->generator->createDocumentForSubject($subject);
    }

    public function testCallCreateDocumentForSubjectWithoutUpdateQuerySetThrowsRuntimeException()
    {
        $this->expectException('RuntimeException');
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
        $data = [
            'id' => 1,
            'field' => null,
        ];
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
