<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Indexer;

use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Markup\NeedleBundle\Elastic\QueryShapeBuilder;

class ElasticsearchIndexingMessager implements IndexingMessagerInterface
{
    private const BATCH_SIZE = 500;

    /**
     * @var Client
     */
    private $elastic;

    /**
     * @var SubjectDataMapperProvider
     */
    private $dataMapperProvider;

    public function __construct(Client $elastic, SubjectDataMapperProvider $dataMapperProvider)
    {
        $this->elastic = $elastic;
        $this->dataMapperProvider = $dataMapperProvider;
    }

    public function executeIndex(
        IndexingMessageInterface $message,
        ?callable $perSubjectCallback = null
    ): IndexingResultInterface {
        $corpus = $message->getCorpus();

        $preDeleteQuery = $message->getPreDeleteQuery();

        //pre-delete non-atomically for just now (though this is dangerous)
        if ($message->isFullReindex()) {
            try {
                $this->elastic->indices()->delete(['index' => $corpus]);
            } catch (Missing404Exception $e) {
                //the index didn't previously exist, but that's OK
            }
        } elseif ($preDeleteQuery !== null) {
            $this->elastic->deleteByQuery(
                [
                    'index' => $corpus,
                    'type' => '_doc',
                    'query' => (new QueryShapeBuilder())->getQueryShapeForFilterQuery($preDeleteQuery),
                ]
            );
        }

        $callback = $perSubjectCallback ?? function () {
            };
        $subjectMapper = $this->dataMapperProvider->fetchMapperForCorpus($message->getCorpus());

        $batch = [];

        foreach ($message->getSubjectIteration() as $subject) {
            $data = $subjectMapper->mapSubjectToData($subject);
            $batch[] = [
                'index' => [
                    '_id' => $data['id'],
                    '_index' => $corpus,
                    '_type' => '_doc',
                ],
            ];

            $batch[] = $data;

            if (count($batch) === self::BATCH_SIZE) {
                $this->elastic->bulk(['body' => $batch]);

                $batch = [];
            }

            $callback();
        }

        if (count($batch) > 0) {
            $this->elastic->bulk(['body' => $batch]);
        }

        //fake it until we make it
        return new IndexingResult(
            true,
            200,
            1,
            'elasticsearch'
        );
    }
}
