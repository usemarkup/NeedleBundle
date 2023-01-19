<?php

namespace Markup\NeedleBundle\Builder;

use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Exception\UnformableSearchKeyException;
use Markup\NeedleBundle\Facet\RangeFacetInterface;
use Markup\NeedleBundle\Filter;
use Markup\NeedleBundle\Lucene\BoostLucenifier;
use Markup\NeedleBundle\Lucene\FilterQueryLucenifier;
use Markup\NeedleBundle\Lucene\SearchTermProcessor;
use Markup\NeedleBundle\Query\ResolvedSelectQueryInterface;
use Markup\NeedleBundle\Sort\SortCollectionInterface;
use Solarium\Component\Facet\Field;
use Solarium\Component\Facet\Range;
use Solarium\QueryType\Select\Query\Query as SolariumQuery;
use Solarium\QueryType\Select\Query\Query;

/**
 * An object that can build a Solarium select query that maps a generic select search query.
 */
class SolariumSelectQueryBuilder
{
    use DedupeFilterQueryTrait;

    const ALL_SIGNIFIER = '*:*';

    /**
     * Whether the builder should request that Solr returns debug information.
     *
     * @var bool
     **/
    private $provideDebugOutput;

    /**
     * @var FilterQueryLucenifier
     **/
    private $lucenifier;

    public function __construct(
        bool $provideDebugOutput = false,
        ?FilterQueryLucenifier $lucenifier = null
    ) {
        $this->provideDebugOutput = $provideDebugOutput;
        $this->lucenifier = $lucenifier ?? new FilterQueryLucenifier();
    }

    /**
     * Builds, and returns, a Solarium select query that maps a generic select search query 1:1.
     *
     * @return SolariumQuery
     **/
    public function buildSolariumQueryFromGeneric(
        ResolvedSelectQueryInterface $query,
        callable $solariumQueryGenerator
    ) {
        /** @var Query $solariumQuery */
        $solariumQuery = $solariumQueryGenerator();

        //if there is a search term, set it
        if ($query->hasSearchTerm()) {
            $rawTerm = is_string($query->getSearchTerm()) ? $query->getSearchTerm() : '';
            $termProcessor = new SearchTermProcessor();
            $luceneTerm = ($query->shouldUseFuzzyMatching())
                ? $termProcessor->process($rawTerm, SearchTermProcessor::FILTER_NORMALIZE | SearchTermProcessor::FILTER_FUZZY_MATCHING)
                : $termProcessor->process($rawTerm);
            $solariumQuery->setQuery($luceneTerm);
        }

        //determine whether we are using the facet values for an underlying "base" (recorded) query
        $shouldUseFacetValuesForRecordedQuery = $query->shouldUseFacetValuesForRecordedQuery();

        //if there are filter queries, add them
        $filterQueries = $query->getFilterQueries();
        $extraLuceneFilters = [];

        if ($shouldUseFacetValuesForRecordedQuery) {
            //pick out Lucene representations for the filters being applied on top of an underlying "base" query
            if (null === $query->getRecord()) {
                throw new \LogicException('Query was expected to contain a query recording.');
            }
            $extraLuceneFilters = array_values(
                array_diff(
                    $this->lucenifyFilterQueries($filterQueries),
                    $this->lucenifyFilterQueries($query->getRecord()->getFilterQueries())
                )
            );
        }

        if (!empty($filterQueries)) {
            // TODO - move deduping to ResolvedSelectQuery
            $filterQueries = $this->dedupeFilterQueries($filterQueries);

            foreach ($filterQueries as $filterQuery) {
                $luceneFilter = $this->lucenifyFilterQuery($filterQuery);
                $solariumFilterQuery = $solariumQuery
                    ->createFilterQuery($filterQuery->getFilter()->getName())
                    ->setQuery($luceneFilter)
                    ->addTag($filterQuery->getFilter()->getName());

                if ($shouldUseFacetValuesForRecordedQuery && in_array($luceneFilter, $extraLuceneFilters)) {
                    $solariumFilterQuery->addTag(sprintf('fq%u', array_search($luceneFilter, $extraLuceneFilters)));
                }
            }
        }

        //if there are fields specified, set them
        $fields = $query->getFields();
        if (!empty($fields)) {
            $solariumQuery->setFields(array_map(
                function ($field) {
                    if ($field instanceof AttributeInterface) {
                        return $field->getSearchKey(['prefer_parsed' => false]);
                    }

                    return strval($field);
                },
                $fields
            ));
        }

        //if there are facets to request, request them
        $facets = $query->getFacets();
        if (!empty($facets)) {
            $facetNamesToExclude = $query->getFacetNamesToExclude();

            $usingFacetComponent = false;

            foreach ($facets as $facet) {
                //if query indicates we should skip this facet, then skip it
                if (false !== array_search($facet->getSearchKey(), $facetNamesToExclude)) {
                    continue;
                }
                $usingFacetComponent = true;
                $solariumFacets = [];
                //check whether to request missing facet values
                $checkMissingFacetValues = $query->shouldRequestFacetValueForMissing() ?: false;

                //if it's a range facet, create accordingly
                if ($facet instanceof RangeFacetInterface) {
                    $solariumFacets[] = $solariumQuery
                        ->getFacetSet()
                        ->createFacetRange($facet->getSearchKey())
                        ->setStart((string) $facet->getRangesStart())
                        ->setEnd((string) $facet->getRangesEnd())
                        ->setGap((string) $facet->getRangeSize());
                } else {
                    $facetSortOrder = $query->getSortOrderForFacet($facet);

                    if ($shouldUseFacetValuesForRecordedQuery) {
                        $solariumFacets[] = $solariumQuery
                            ->getFacetSet()
                            ->createFacetField(sprintf('include_%s', $facet->getSearchKey()))
                            ->setMinCount(1)
                            ->setMissing($checkMissingFacetValues)
                            ->setSort($facetSortOrder ?: 'index');
                        $solariumFacets[] = $solariumQuery
                            ->getFacetSet()
                            ->createFacetField(sprintf('exclude_%s', $facet->getSearchKey()))
                            ->setMinCount(1)
                            ->setMissing($checkMissingFacetValues)
                            ->setSort($facetSortOrder ?: 'index')
                            ->addExcludes(array_map(function ($key) {
                                return sprintf('fq%u', $key);
                            }, array_keys($extraLuceneFilters)));
                    } else {
                        $solariumFacets[] = $solariumQuery
                            ->getFacetSet()
                            ->createFacetField($facet->getSearchKey())
                            ->setMinCount(1) //sets default mincount of 1, so a facet value needs at least one corresponding result to show
                            ->setMissing($checkMissingFacetValues)
                            ->setSort($facetSortOrder ?: 'index');
                    }
                }

                foreach ($solariumFacets as $solariumFacet) {
                    if ($solariumFacet instanceof Field || $solariumFacet instanceof Range) {
                        $solariumFacet
                            ->setField($facet->getSearchKey());
                    }

                    //set Solr local params to exclude filter values
                    if ($query->getWhetherFacetIgnoresCurrentFilters($facet)) {
                        $solariumFacet->addExclude($facet->getName());
                    }
                }
            }
            //by default, remove the limit on facet results
            //Solr has a default limit of 100, but a negative value removes it
            //@see https://wiki.apache.org/solr/SimpleFacetParameters#facet.limit
            if ($usingFacetComponent) {
                $solariumQuery->getFacetSet()->setLimit(-1);
            }
        }

        //if there are boost query fields to apply, apply them, and switch the search engine to edismax from lucene
        $boostQueryFields = $query->getBoostQueryFields();

        if (!empty($boostQueryFields)) {
            //set to using edismax
            $edismax = $solariumQuery->getEDisMax();
            //set query/ query alternative to all if applicable
            if ($solariumQuery->getQuery() === self::ALL_SIGNIFIER) {
                $solariumQuery->setQuery('');
                $edismax->setQueryAlternative(self::ALL_SIGNIFIER);
            }
            //apply boosts
            $queryFields = [];
            $boostLucenifier = new BoostLucenifier();
            foreach ($boostQueryFields as $boostField) {
                $queryFields[] = $boostLucenifier->lucenifyBoost($boostField);
            }
            $edismax->setQueryFields(implode(' ', $queryFields));
        }

        //if there are sorts to apply, apply them
        $sortCollection = $query->getSortCollection();
        if ($sortCollection instanceof SortCollectionInterface) {
            foreach ($sortCollection as $sort) {
                try {
                    $sortKey = $sort->getFilter()->getSearchKey();
                } catch (UnformableSearchKeyException $e) {
                    //it's just a sort, so continue
                    continue;
                }
                $solariumQuery->addSort(
                    $sortKey,
                    ($sort->isDescending()) ? SolariumQuery::SORT_DESC : SolariumQuery::SORT_ASC
                );
            }
        }

        //if there is a spellcheck request to apply, apply it
        if (null !== $query->getSpellcheck()) {
            $solariumSpellcheck = $solariumQuery->getSpellcheck();
            if (null !== $query->getSpellcheck()->getResultLimit()) {
                $solariumSpellcheck->setCount($query->getSpellcheck()->getResultLimit());
            }
            $solariumSpellcheck->setDictionary($query->getSpellcheck()->getDictionary());
        }

        // if grouping on a field then group - and apply grouping sort if applicable
        if ($query->getGroupingField() !== null) {
            $groupComponent = $solariumQuery->getGrouping();
            $groupComponent->addField($query->getGroupingField());
            $groupComponent->setLimit(1000);
            $groupComponent->setMainResult(false);
            $groupComponent->setNumberOfGroups(true);

            $groupingSortCollection = $query->getGroupingSortCollection();
            if ($groupingSortCollection instanceof SortCollectionInterface) {
                $sortStringComponents = [];
                foreach ($groupingSortCollection as $groupingSort) {
                    $sortStringComponents[] = sprintf(
                        '%s %s',
                        $groupingSort->getFilter()->getSearchKey(),
                        ($groupingSort->isDescending()) ? SolariumQuery::SORT_DESC : SolariumQuery::SORT_ASC
                    );
                }
                $groupComponent->setSort(implode(',', $sortStringComponents));
            }
        }

        //if configured to generate debug output, and there is a search term, request debug output
        if ($query->hasSearchTerm() and $this->provideDebugOutput) {
            $solariumQuery->getDebug(); //this switches debug on
        }

        return $solariumQuery;
    }

    /**
     * Turns a filter query into Lucene syntax for use on Solr.
     *
     * @param  Filter\FilterQueryInterface $filterQuery
     * @return string
     **/
    private function lucenifyFilterQuery(Filter\FilterQueryInterface $filterQuery)
    {
        return $this->lucenifier->lucenify($filterQuery);
    }

    /**
     * Turns a collection of filter queries into a collection of Lucene strings.
     *
     * @param Filter\FilterQueryInterface[] $filterQueries
     *
     * @return array
     **/
    private function lucenifyFilterQueries($filterQueries)
    {
        $luceneFilters = [];
        foreach ($filterQueries as $filterQuery) {
            if (!$filterQuery instanceof Filter\FilterQueryInterface) {
                continue;
            }
            $luceneFilters[] = $this->lucenifyFilterQuery($filterQuery);
        }

        return $luceneFilters;
    }
}
