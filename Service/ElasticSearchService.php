<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Service;

use Elasticsearch\Client;
use function GuzzleHttp\Promise\coroutine;
use function GuzzleHttp\Promise\promise_for;
use Markup\NeedleBundle\Adapter\ElasticResultPromisePagerfantaAdapter;
use Markup\NeedleBundle\Builder\ElasticSelectQueryBuilder;
use Markup\NeedleBundle\Builder\QueryBuildOptionsLocator;
use Markup\NeedleBundle\Context\NoopSearchContext;
use Markup\NeedleBundle\Context\SearchContextInterface;
use Markup\NeedleBundle\Elastic\CorpusIndexProvider;
use Markup\NeedleBundle\Query\ResolvedSelectQuery;
use Markup\NeedleBundle\Query\ResolvedSelectQueryInterface;
use Markup\NeedleBundle\Result\ElasticsearchFacetSetsStrategy;
use Markup\NeedleBundle\Result\PagerfantaResultAdapter;
use Markup\NeedleBundle\Result\ResultInterface;
use Pagerfanta\Pagerfanta;

class ElasticSearchService implements AsyncSearchServiceInterface
{
    const HUNNERS = 600;

    /**
     * @var Client
     */
    private $elastic;

    /**
     * @var ElasticSelectQueryBuilder
     */
    private $queryBuilder;

    /**
     * @var QueryBuildOptionsLocator
     */
    private $queryBuildOptionsLocator;

    /**
     * @var CorpusIndexProvider
     */
    private $corpusIndexProvider;

    /**
     * @var string
     */
    private $corpus;

    /**
     * @var array
     */
    private $decorators;

    public function __construct(
        Client $elastic,
        ElasticSelectQueryBuilder $queryBuilder,
        QueryBuildOptionsLocator $queryBuildOptionsLocator,
        CorpusIndexProvider $corpusIndexProvider,
        string $corpus
    ) {
        $this->elastic = $elastic;
        $this->queryBuilder = $queryBuilder;
        $this->queryBuildOptionsLocator = $queryBuildOptionsLocator;
        $this->corpusIndexProvider = $corpusIndexProvider;
        $this->corpus = $corpus;
        $this->decorators = [];
    }

    /**
     * {@inheritDoc}
     */
    public function executeQueryAsync($query, ?SearchContextInterface $searchContext = null)
    {
        return coroutine(
            function () use ($query, $searchContext) {
                if (!$query instanceof ResolvedSelectQueryInterface) {
                    if ($searchContext === null) {
                        $searchContext = new NoopSearchContext();
                    }

                    $query = new ResolvedSelectQuery(
                        $query,
                        $searchContext
                    );
                }

                if (!$query instanceof ResolvedSelectQueryInterface) {
                    throw new \InvalidArgumentException('$query must be of type ResolvedSelectQueryInterface or SelectQueryInterface');
                }

                foreach ($this->decorators as $decorator) {
                    $query = $decorator->decorate($query);
                }
                $elasticQuery = $this->queryBuilder->buildElasticQueryFromGeneric(
                    $query,
                    $this->queryBuildOptionsLocator->get($this->corpus)
                );

                //apply offset/limit
                $maxPerPage = $query->getMaxPerPage();
                $elasticQuery['size'] = $maxPerPage ?: self::HUNNERS;

                $page = $query->getPageNumber();
                if ($page && $maxPerPage) {
                    $elasticQuery['from'] = $maxPerPage * ($page-1);
                }

                $queryParams = [
                    'index' => $this->corpusIndexProvider->getIndexForCorpus($this->corpus),
                    'type' => '_doc',
                    'body' => $elasticQuery,
                    'client' => [
                        'future' => 'lazy',
                    ],
                    'rest_total_hits_as_int' => true,
                ];

                $elasticResult = yield promise_for($this->elastic->search($queryParams));

                $pagerfanta = new Pagerfanta(new ElasticResultPromisePagerfantaAdapter(promise_for($elasticResult)));
                $pagerfanta->setMaxPerPage($maxPerPage ?: self::HUNNERS);
                $pagerfanta->setCurrentPage($page ?: 1);

                $result = new PagerfantaResultAdapter($pagerfanta);

                $result->setFacetSetStrategy(
                    new ElasticsearchFacetSetsStrategy(
                        $elasticResult['aggregations'] ?? [],
                        $query->getFacets(),
                        $query->getFacetCollatorProvider(),
                        $query->getFacetSetDecoratorProvider(),
                        $query->getOriginalSelectQuery()
                    )
                );

                yield $result;
            }
        );
    }

    /**
     * {@inheritDoc}
     */
    public function executeQuery($query, ?SearchContextInterface $searchContext = null): ResultInterface
    {
        return $this->executeQueryAsync($query, $searchContext)->wait();
    }
}
