<?php

declare(strict_types=1);

namespace Markup\NeedleBundle\Service;

use Elasticsearch\Client;
use function GuzzleHttp\Promise\coroutine;
use function GuzzleHttp\Promise\promise_for;
use GuzzleHttp\Promise\PromiseInterface;
use Markup\NeedleBundle\Adapter\ElasticResultPromisePagerfantaAdapter;
use Markup\NeedleBundle\Builder\ElasticSelectQueryBuilder;
use Markup\NeedleBundle\Context\SearchContextInterface;
use Markup\NeedleBundle\Query\ResolvedSelectQuery;
use Markup\NeedleBundle\Query\ResolvedSelectQueryDecoratorInterface;
use Markup\NeedleBundle\Query\SelectQueryInterface;
use Markup\NeedleBundle\Result\ElasticsearchFacetSetsStrategy;
use Markup\NeedleBundle\Result\PagerfantaResultAdapter;
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
     * @var string
     */
    private $corpus;

    /**
     * @var SearchContextInterface|null
     */
    private $searchContext;

    /**
     * @var array
     */
    private $decorators;

    public function __construct(Client $elastic, ElasticSelectQueryBuilder $queryBuilder, string $corpus)
    {
        $this->elastic = $elastic;
        $this->queryBuilder = $queryBuilder;
        $this->corpus = $corpus;
        $this->decorators = [];
    }

    /**
     * Gets a promise which, when being resolved, executes a select query on a service and makes a result available.
     *
     * @param SelectQueryInterface $query
     * @return PromiseInterface
     **/
    public function executeQueryAsync(SelectQueryInterface $query)
    {
        return coroutine(
            function () use ($query) {
                $query = new ResolvedSelectQuery($query, $this->searchContext);

                foreach ($this->decorators as $decorator) {
                    $query = $decorator->decorate($query);
                }
                $elasticQuery = $this->queryBuilder->buildElasticQueryFromGeneric($query);

                //apply offset/limit
                $maxPerPage = $query->getMaxPerPage();
                if (null === $maxPerPage && $this->searchContext && $query->getPageNumber() !== null) {
                    $maxPerPage = $this->searchContext->getItemsPerPage() ?: null;
                }
                $elasticQuery['size'] = $maxPerPage ?: self::HUNNERS;

                $page = $query->getPageNumber();
                if ($page && $maxPerPage) {
                    $elasticQuery['from'] = $maxPerPage * ($page-1);
                }

                $queryParams = [
                    'index' => $this->corpus,
                    'type' => '_doc',
                    'body' => $elasticQuery,
                    'client' => [
                        'future' => 'lazy',
                    ],
                ];

                $elasticResult = yield promise_for($this->elastic->search($queryParams));

                $pagerfanta = new Pagerfanta(new ElasticResultPromisePagerfantaAdapter(promise_for($elasticResult)));
                $pagerfanta->setMaxPerPage($maxPerPage ?: self::HUNNERS);
                $pagerfanta->setCurrentPage($page ?: 1);

                $result = new PagerfantaResultAdapter($pagerfanta);

                if (!is_null($this->searchContext)) {
                    $result->setFacetSetStrategy(
                        new ElasticsearchFacetSetsStrategy(
                            $elasticResult['aggregations'] ?? [],
                            $this->searchContext,
                            $query->getRecord()
                        )
                    );
                }

                yield $result;
            }
        );
    }

    /**
     * Executes a select query on a service and returns a result.
     *
     * @param SelectQueryInterface $query
     * @return \Markup\NeedleBundle\Result\ResultInterface
     **/
    public function executeQuery(SelectQueryInterface $query)
    {
        return $this->executeQueryAsync($query)->wait();
    }

    /**
     * Sets a context on the search service, which is a contextual object that can determine aspects of the search to execute, agnostic of the actual search implementation.
     *
     * @param SearchContextInterface $context
     **/
    public function setContext(SearchContextInterface $context)
    {
        $this->searchContext = $context;
    }

    /**
     * Adds a decorator that will decorate the ResolvedSelectQuery during execution
     * directly after the SelectQuery has been combined with the SearchContext
     *
     * @param ResolvedSelectQueryDecoratorInterface $decorator
     **/
    public function addDecorator(ResolvedSelectQueryDecoratorInterface $decorator)
    {
        $this->decorators[] = $decorator;
    }
}
