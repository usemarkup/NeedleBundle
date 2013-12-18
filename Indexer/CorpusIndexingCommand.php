<?php

namespace Markup\NeedleBundle\Indexer;

use Markup\NeedleBundle\Corpus\CorpusProvider;
use Markup\NeedleBundle\Filter\FilterQueryInterface;
use Markup\NeedleBundle\Lucene\FilterQueryLucenifier;
use Solarium\Client as Solarium;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use AppendIterator;

/**
* A command for indexing a corpus.
*/
class CorpusIndexingCommand
{
    const ALL_SIGNIFIER = '*:*';

    /**
     * @var CorpusProvider
     **/
    private $corpusProvider;

    /**
     * A Solarium client object.
     *
     * @var Solarium
     **/
    private $solarium;

    /**
     * A mapper to get document data for a subject.
     *
     * @var SubjectDataMapperProvider
     **/
    private $subjectMapperProvider;

    /**
     * @var FilterQueryLucenifier
     **/
    private $filterQueryLucenifier;

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
     * @var AppendIterator
     **/
    private $wrappingIterator;

    /**
     * @var string
     **/
    private $corpusName;

    /**
     * @var bool
     **/
    private $iteratorAppended;

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
     * @param CorpusProvider            $corpusProvider
     * @param Solarium                  $solarium
     * @param SubjectDataMapperProvider $subjectMapperProvider
     * @param FilterQueryLucenifier     $filterQueryLucenifier
     * @param bool                      $shouldPreDelete
     * @param LoggerInterface           $logger
     * @param AppendIterator            $wrappingIterator
     **/
    public function __construct(
        CorpusProvider $corpusProvider,
        Solarium $solarium,
        SubjectDataMapperProvider $subjectMapperProvider,
        FilterQueryLucenifier $filterQueryLucenifier,
        $shouldPreDelete = false,
        LoggerInterface $logger = null,
        AppendIterator $wrappingIterator = null)
    {
        $this->corpusProvider = $corpusProvider;
        $this->solarium = $solarium;
        $this->subjectMapperProvider = $subjectMapperProvider;
        $this->filterQueryLucenifier = $filterQueryLucenifier;
        $this->shouldPreDelete = $shouldPreDelete;
        $this->logger = $logger ?: new NullLogger();
        $this->wrappingIterator = $wrappingIterator ?: new AppendIterator();
        $this->iteratorAppended = false;
    }

    public function __invoke()
    {
        if (empty($this->corpusName)) {
            throw new \BadMethodCallException('You need to set a corpus name on the corpus indexing command in order to execute it.');
        }
        $logger = $this->getLogger();
        $logger->info(sprintf('Indexing of corpus "%s" started.', $this->getCorpus()->getName()));
        $logger->debug(sprintf('Memory usage before search indexing process: %01.0fKB.', memory_get_usage(true) / 1024));
        $startTime = microtime(true);
        $wrappingIterator = $this->getWrappingIterator();
        if (!$this->iteratorAppended) {
            $wrappingIterator->append($this->getSubjectIteration());
            $this->iteratorAppended = true;
        }
        $subjects = $wrappingIterator;
        $updateQuery = $this->getSolariumClient()->createUpdate();
        //initially delete all indexes - todo allow disambiguation between types of document
        if ($this->shouldPreDelete) {
            $updateQuery->addDeleteQuery($this->getDeleteQueryLucene());
        }
        $documentGenerator = new SubjectDocumentGenerator($this->getSubjectMapper());
        $documentGenerator->setUpdateQuery($updateQuery);
        $updateQuery->addDocuments(new DocumentFilterIterator(new SubjectDocumentIterator($subjects, $documentGenerator)));
        $updateQuery->addCommit();
        $updateQuery->addOptimize();
        $result = $this->getSolariumClient()->update($updateQuery);
        $logger->debug(sprintf('Status code of Solr query: %s. Query time: %ums.', $result->getStatus(), $result->getQueryTime()));
        $logger->debug(sprintf('Memory usage after search indexing process: %01.0fKB.', memory_get_usage(true) / 1024));
        $endTime = microtime(true);
        $logger->info(sprintf('Indexing of corpus "%s" completed successfully in %01.3fs.', $this->getCorpus()->getName(), $endTime-$startTime));
    }

    /**
     * Sets the name of the corpus to be indexed.
     *
     * @param  string $corpusName
     * @return self
     **/
    public function setCorpusName($corpusName)
    {
        $this->corpusName = $corpusName;

        return $this;
    }

    /**
     * @param  LoggerInterface $logger
     * @return self
     **/
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param AppendIterator $wrappingIterator
     **/
    public function setWrappingIterator(AppendIterator $wrappingIterator)
    {
        $this->wrappingIterator = $wrappingIterator;

        return $this;
    }

    /**
     * @return AppendIterator
     **/
    private function getWrappingIterator()
    {
        return $this->wrappingIterator;
    }

    /**
     * @return Solarium
     **/
    private function getSolariumClient()
    {
        return $this->solarium;
    }

    /**
     * @return CorpusInterface
     **/
    private function getCorpus()
    {
        return $this->corpusProvider->fetchNamedCorpus($this->corpusName);
    }

    /**
     * Sets the subject iteration on the command. Without this, the iteration used will be provided by the corpus.
     *
     * @param  \Iterator $iteration
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
     * @return \Iterator
     **/
    private function getSubjectIteration()
    {
        if (null !== $this->subjectIteration) {
            return $this->subjectIteration;
        }

        return $this->getCorpus()->getSubjectIteration();
    }

    /**
     * Sets whether the export should issue a directive before the update on the backend that a delete query should be run.
     *
     * @param  bool $whether
     * @return self
     **/
    public function setShouldPreDelete($whether)
    {
        $this->shouldPreDelete = $whether;

        return $this;
    }

    /**
     * @return SubjectDataMapperInterface
     **/
    private function getSubjectMapper()
    {
        return $this->subjectMapperProvider->fetchMapperForCorpus($this->getCorpus()->getName());
    }

    /**
     * @param  FilterQueryInterface $deleteQuery
     * @return self
     **/
    public function setDeleteQuery(FilterQueryInterface $deleteQuery)
    {
        $this->deleteQuery = $deleteQuery;
    }

    /**
     * @return string
     **/
    private function getDeleteQueryLucene()
    {
        if (null === $this->deleteQuery) {
            return self::ALL_SIGNIFIER;
        }

        return $this->filterQueryLucenifier->lucenify($this->deleteQuery);
    }

    /**
     * Gets the logger object set on this command.
     *
     * @return LoggerInterface
     **/
    private function getLogger()
    {
        return $this->logger;
    }
}
