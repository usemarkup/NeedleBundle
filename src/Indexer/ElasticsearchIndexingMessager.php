<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Indexer;

use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Markup\NeedleBundle\Elastic\CorpusIndexConfiguration;
use Markup\NeedleBundle\Elastic\CorpusIndexProvider;
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

    /**
     * @var CorpusIndexProvider
     */
    private $corpusIndexProvider;

    /**
     * @var CorpusIndexConfiguration
     */
    private $corpusIndexConfiguration;

    public function __construct(
        Client $elastic,
        SubjectDataMapperProvider $dataMapperProvider,
        CorpusIndexProvider $corpusIndexProvider,
        CorpusIndexConfiguration $corpusIndexConfiguration
    ) {
        $this->elastic = $elastic;
        $this->dataMapperProvider = $dataMapperProvider;
        $this->corpusIndexProvider = $corpusIndexProvider;
        $this->corpusIndexConfiguration = $corpusIndexConfiguration;
    }

    public function executeIndex(
        IndexingMessageInterface $message,
        ?callable $perSubjectCallback = null
    ): IndexingResultInterface {
        $index = $this->corpusIndexProvider->getIndexForCorpus($message->getCorpus());

        $preDeleteQuery = $message->getPreDeleteQuery();

        //pre-delete non-atomically for just now (though this is dangerous)
        if ($message->isFullReindex()) {
            try {
                $this->elastic->indices()->delete(['index' => $index]);
            } catch (Missing404Exception $e) {
                //the index didn't previously exist, but that's OK
            }

            $body = [];
            $settings = $this->corpusIndexConfiguration->getSettings($message->getCorpus());
            $mappings = $this->corpusIndexConfiguration->getMappings($message->getCorpus());

            if (!empty($settings)) {
                $body['settings'] = $settings;
            }
            if (!empty($mappings)) {
                $body['mappings'] = $mappings;
            }

            $this->elastic->indices()->create(
                [
                    'index' => $index,
                    'body' => $body,
                    'include_type_name' => true,
                ]
            );
        } elseif ($preDeleteQuery !== null) {
            $this->elastic->deleteByQuery(
                [
                    'index' => $index,
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
                    '_index' => $index,
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
