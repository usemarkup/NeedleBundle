<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Tests\Indexer;

use Markup\NeedleBundle\Filter\FilterQueryInterface;
use Markup\NeedleBundle\Indexer\IndexingMessage;
use Markup\NeedleBundle\Indexer\IndexingMessageInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class IndexingMessageTest extends MockeryTestCase
{
    /**
     * @var \Iterator
     */
    private $subjectIteration;

    /**
     * @var FilterQueryInterface|m\MockInterface
     */
    private $preDeleteQuery;

    /**
     * @var IndexingMessage
     */
    private $message;

    protected function setUp()
    {
        $this->subjectIteration = new \ArrayIterator(['subject', 'subject', 'subject']);
        $this->preDeleteQuery = m::mock(FilterQueryInterface::class);
        $this->message = new IndexingMessage(
            $this->subjectIteration,
            'corpus',
            true,
            $this->preDeleteQuery
        );
    }

    public function testIsIndexingMessage()
    {
        $this->assertInstanceOf(IndexingMessageInterface::class, $this->message);
    }

    public function testGetSubjectIteration()
    {
        $this->assertSame($this->subjectIteration, $this->message->getSubjectIteration());
    }

    public function testGetCorpus()
    {
        $this->assertEquals('corpus', $this->message->getCorpus());
    }

    public function testIsFullIndex()
    {
        $this->assertTrue($this->message->isFullReindex());
    }

    public function testGetPreDeleteQuery()
    {
        $this->assertSame($this->preDeleteQuery, $this->message->getPreDeleteQuery());
    }
}
