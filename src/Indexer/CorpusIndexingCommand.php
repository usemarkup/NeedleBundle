<?php

namespace Markup\NeedleBundle\Indexer;

use Markup\NeedleBundle\Corpus\CorpusInterface;
use Markup\NeedleBundle\Corpus\CorpusProvider;
use Markup\NeedleBundle\Event\CorpusPostUpdateEvent;
use Markup\NeedleBundle\Event\CorpusPreUpdateEvent;
use Markup\NeedleBundle\Event\SearchEvents;
use Markup\NeedleBundle\Filter\FilterQueryInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * A command for indexing a corpus.
 */
class CorpusIndexingCommand
{
    /**
     * @var CorpusProvider
     **/
    private $corpusProvider;

    /**
     * @var ContainerInterface
     */
    private $messagerLocator;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var bool
     **/
    private $shouldPreDelete;

    /**
     * A logger object.
     *
     * @var LoggerInterface
     **/
    private $logger;

    /**
     * An iteration over the subjects.
     *
     * @var \Iterator
     **/
    private $subjectIteration;

    /**
     * @var FilterQueryInterface
     **/
    private $deleteQuery;

    /**
     * @var callable
     */
    private $perSubjectCallback;

    public function __construct(
        CorpusProvider $corpusProvider,
        IndexingMessagerLocator $messagerLocator,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger = null
    ) {
        $this->corpusProvider = $corpusProvider;
        $this->messagerLocator = $messagerLocator;
        $this->eventDispatcher = $eventDispatcher;
        $this->shouldPreDelete = false;
        $this->logger = $logger ?: new NullLogger();
        $this->perSubjectCallback = function () {
        };
    }

    public function __invoke(string $corpus)
    {
        $isFullUpdate = $this->shouldPreDelete;
        $corpusObj = $this->getCorpus($corpus);
        if (!$corpusObj) {
            throw new \OutOfRangeException(sprintf('A corpus with name "%s" is not registered.', $corpus));
        }
        $this->eventDispatcher->dispatch(
            SearchEvents::CORPUS_PRE_UPDATE,
            new CorpusPreUpdateEvent($corpusObj, $isFullUpdate)
        );
        $this->logger->info(sprintf('Indexing of corpus "%s" started.', $corpus));
        $this->logger->debug(sprintf(
            'Memory usage before search indexing process: %01.0fMB.',
            (memory_get_usage(true) / 1024) / 1024
        ));
        /** @var IndexingMessagerInterface $messager */
        $messager = $this->messagerLocator->get($corpus);
        $message = new IndexingMessage(
            $this->getSubjectIteration($corpus),
            $corpus,
            $isFullUpdate,
            $this->deleteQuery
        );
        $startTime = microtime(true);
        $result = $messager->executeIndex($message, $this->perSubjectCallback);
        $this->logger->debug(sprintf(
            'Status code of query on search backend: %s. Query time: %ums.',
            $result->getStatusCode(),
            $result->getQueryTimeInMilliseconds()
        ));
        $this->logger->debug(sprintf(
            'Memory usage after search indexing process: %01.0fMB.',
            (memory_get_usage(true) / 1024) / 1024
        ));
        $endTime = microtime(true);
        $this->logger->info(sprintf(
            'Indexing of corpus "%s" completed successfully in %01.3fs.',
            $corpus,
            $endTime - $startTime
        ));
        $this->eventDispatcher->dispatch(
            SearchEvents::CORPUS_POST_UPDATE,
            new CorpusPostUpdateEvent($corpusObj, $isFullUpdate, $result)
        );
    }

    /**
     * @param LoggerInterface $logger
     * @return self
     **/
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    public function setPerSubjectCallback(callable $callback)
    {
        $this->perSubjectCallback = $callback;
    }

    private function getCorpus(string $corpusName): ?CorpusInterface
    {
        return $this->corpusProvider->fetchNamedCorpus($corpusName);
    }

    /**
     * Sets the subject iteration on the command. Without this, the iteration used will be provided by the corpus.
     *
     * @param \Iterator $iteration
     * @return self
     **/
    public function setSubjectIteration(\Iterator $iteration)
    {
        $this->subjectIteration = $iteration;

        return $this;
    }

    /**
     * Gets the subject iteration to go over. Falls back to the corpus subject iteration if an explicit one has not been provided.
     *
     * @param string $corpus
     * @return \Iterator
     **/
    private function getSubjectIteration(string $corpus)
    {
        if (null !== $this->subjectIteration) {
            return $this->subjectIteration;
        }
        $corpus = $this->getCorpus($corpus);
        if (!$corpus) {
            return new \ArrayIterator();
        }

        return $this->formIteratorFor($corpus->getSubjectIteration());
    }

    /**
     * Sets whether the export should issue a directive before the update on the backend that a delete query should be run.
     *
     * @param bool $whether
     * @return self
     **/
    public function setShouldPreDelete($whether)
    {
        $this->shouldPreDelete = $whether;

        return $this;
    }

    /**
     * @param FilterQueryInterface $deleteQuery
     * @return self
     **/
    public function setDeleteQuery(FilterQueryInterface $deleteQuery)
    {
        $this->deleteQuery = $deleteQuery;

        return $this;
    }

    /**
     * @param array|\Traversable $iterable
     * @return \Iterator
     */
    private function formIteratorFor($iterable)
    {
        if ($iterable instanceof \Iterator) {
            return $iterable;
        }
        if ($iterable instanceof \Traversable) {
            return new \IteratorIterator($iterable);
        }
        if (is_array($iterable)) {
            return new \ArrayIterator($iterable);
        }

        return new \ArrayIterator([$iterable]);
    }
}
