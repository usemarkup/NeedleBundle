<?php

namespace Markup\NeedleBundle\Tests\Indexer;

use Doctrine\Common\Collections\Collection;
use Markup\NeedleBundle\Indexer\SubjectDocumentGeneratorInterface;
use Markup\NeedleBundle\Indexer\SubjectDocumentIterator;
use Solarium\QueryType\Update\Query\Document\Document;

/**
* A test for an iterator that can go over an iteration of subjects and emit documents for Solarium.
*/
class SubjectDocumentIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testIsIterator()
    {
        $it = new \ReflectionClass(SubjectDocumentIterator::class);
        $this->assertTrue($it->implementsInterface(\Iterator::class));
    }

    public function testStringInConstructorForSubjectsThrowsInvalidArgumentException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $docGenerator = $this->createMock(SubjectDocumentGeneratorInterface::class);
        $it = new SubjectDocumentIterator('subjects', $docGenerator);
    }

    public function testEmitExpectedNumberOfDocuments()
    {
        $subject = new \stdClass();

        $allSubjects = [$subject, $subject, $subject, $subject];
        $docGenerator = $this->createMock(SubjectDocumentGeneratorInterface::class);
        $doc = $this->createMock(Document::class);
        $docGenerator
            ->expects($this->any())
            ->method('createDocumentForSubject')
            ->will($this->returnValue($doc));
        $it = new SubjectDocumentIterator($allSubjects, $docGenerator);
        $this->assertEquals(4, iterator_count($it));
        $this->assertContainsOnly(Document::class, iterator_to_array($it));
    }

    public function testSetAndGetSubjects()
    {
        $docGenerator = $this->createMock(SubjectDocumentGeneratorInterface::class);
        $it = new SubjectDocumentIterator([], $docGenerator);
        $this->assertCount(0, iterator_to_array($it->getSubjects()));
        $subject = new \stdClass();
        $someSubjects = [$subject, $subject, $subject];
        $it->setSubjects($someSubjects);
        $this->assertEquals($someSubjects, iterator_to_array($it->getSubjects()));
    }

    public function testCallbacksExecuted()
    {
        $subject = $this->createMock(Collection::class);
        $subject
            ->expects($this->once())
            ->method('get');
        $callback = function ($subject) {
            $subject->get('skdjhfskjdfh');
        };
        $subjects = [$subject];
        $docGenerator = $this->createMock(SubjectDocumentGeneratorInterface::class);
        $it = new SubjectDocumentIterator($subjects, $docGenerator, [$callback]);
        iterator_to_array($it);
    }
}
