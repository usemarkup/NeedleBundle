<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Indexer;

use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;

class ElasticsearchIndexingMessager implements IndexingMessagerInterface
{
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

    public function executeIndex(IndexingMessageInterface $message, ?callable $perSubjectCallback = null): IndexingResultInterface
    {
        $corpus = $message->getCorpus();

        $subjectMapper = $this->dataMapperProvider->fetchMapperForCorpus($message->getCorpus());
        $bucket = (function ($subjects) use ($subjectMapper) {
            foreach ($subjects as $article) {
                yield $subjectMapper->mapSubjectToData($article);
            }
        })($message->getSubjectIteration());

        $paramsForBody = function ($body) use ($corpus) {
            return [
                'index' => $corpus,
                'type' => '_doc',
                'body' => $body,
            ];
        };

        $callback = $perSubjectCallback ?? function () {};

        $mapBucketToBody = function ($bucket) use ($corpus, $callback) {
            foreach ($bucket as $item) {
                yield [
                    'index' => [
                        '_index' => $corpus,
                        '_type' => '_doc',
                        '_id' => $item['id'],
                    ],
                ];
                yield $item;
                $callback();
            }
        };

        $sendBodies = function ($bucket) use ($mapBucketToBody, $paramsForBody) {
            $this->elastic->bulk($paramsForBody($mapBucketToBody($bucket)));
        };

        //pre-delete non-atomically for just now (though this is dangerous)
        if ($message->isFullReindex()) {
            try {
                $this->elastic->indices()->delete(['index' => $corpus]);
            } catch (Missing404Exception $e) {
                //the index didn't previously exist, but that's OK
            }
        }

        $sendBodies($bucket);

        //fake it until we make it
        return new IndexingResult(
            true,
            200,
            1,
            'elasticsearch'
        );
    }
}
