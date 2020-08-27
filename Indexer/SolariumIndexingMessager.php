<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Indexer;

use Markup\NeedleBundle\Filter\FilterQueryInterface;
use Markup\NeedleBundle\Lucene\FilterQueryLucenifier;
use Markup\NeedleBundle\Result\SolariumUpdateResult;
use Solarium\Client as Solarium;
use Solarium\Core\Client\Endpoint;

class SolariumIndexingMessager implements IndexingMessagerInterface
{
    const LUCENE_ALL_SIGNIFIER = '*:*';

    /**
     * @var Solarium
     */
    private $solarium;

    /**
     * @var SubjectDataMapperProvider
     */
    private $subjectDataMapperProvider;

    public function __construct(Solarium $solarium, SubjectDataMapperProvider $subjectDataMapperProvider)
    {
        $this->solarium = $solarium;
        $this->subjectDataMapperProvider = $subjectDataMapperProvider;
    }

    public function executeIndex(IndexingMessageInterface $message, ?callable $perSubjectCallback = null): IndexingResultInterface
    {
        $updateQuery = $this->solarium->createUpdate();
        $preDeleteQuery = $message->getPreDeleteQuery();

        if ($preDeleteQuery || $message->isFullReindex()) {
            $endPoint = $this->solarium->getOption('endpoint')[0] ?? null;

            if ($endPoint instanceof Endpoint) {
                /**
                 * For a FULL Index increase the timeout by 10x
                 * to allow a large payload to be sent to Solr
                 */
                $endPoint->setTimeout(intval($endPoint->getTimeout()) * 10);
            }
            $updateQuery->addDeleteQuery($this->createLuceneFilterQuery($preDeleteQuery));
        }

        $subjectMapper = $this->subjectDataMapperProvider->fetchMapperForCorpus($message->getCorpus());
        $documentGenerator = new SubjectDocumentGenerator($subjectMapper, false);
        $documentGenerator->setUpdateQuery($updateQuery);
        $updateQuery->addDocuments(
            new DocumentFilterIterator(
                new SubjectDocumentIterator(
                    $message->getSubjectIteration(),
                    $documentGenerator,
                    $perSubjectCallback
                )
            )
        );
        $updateQuery->addCommit();
        $updateQuery->addOptimize();

        return new SolariumUpdateResult($this->solarium->update($updateQuery));
    }

    private function createLuceneFilterQuery(?FilterQueryInterface $filterQuery): string
    {
        if (null === $filterQuery) {
            return self::LUCENE_ALL_SIGNIFIER;
        }

        return (new FilterQueryLucenifier())->lucenify($filterQuery->getSearchKey(), $filterQuery->getFilterValue());
    }
}
