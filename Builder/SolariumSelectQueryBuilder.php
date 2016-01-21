<?php

namespace Markup\NeedleBundle\Builder;

use Markup\NeedleBundle\Facet\RangeFacetInterface;
use Markup\NeedleBundle\Filter;
use Markup\NeedleBundle\Lucene\FilterQueryLucenifier;
use Markup\NeedleBundle\Query\ResolvedSelectQueryInterface;
use Markup\NeedleBundle\Sort\SortCollectionInterface;
use Solarium\Client as SolariumClient;
use Solarium\QueryType\Select\Query\Query as SolariumQuery;

/**
 * An object that can build a Solarium select query that maps a generic select search query.
 */
class SolariumSelectQueryBuilder
{
    const ALL_SIGNIFIER = '*:*';

    /**
     * A Solarium client.
     *
     * @var SolariumClient
     **/
    private $solarium;

    /**
     * @var FilterQueryLucenifier
     **/
    private $lucenifier;

    /**
     * Whether the builder should request that Solr returns debug information.
     *
     * @var bool
     **/
    private $provideDebugOutput;

    /**
     * @param SolariumClient        $solarium
     * @param FilterQueryLucenifier $lucenifier
     * @param bool                  $provideDebugOutput
     **/
    public function __construct(SolariumClient $solarium, FilterQueryLucenifier $lucenifier, $provideDebugOutput = false)
    {
        $this->solarium = $solarium;
        $this->lucenifier = $lucenifier;
        $this->provideDebugOutput = $provideDebugOutput;
    }

    /**
     * Builds, and returns, a Solarium select query that maps a generic select search query 1:1.
     *
     * @return SolariumQuery
     **/
    public function buildSolariumQueryFromGeneric(ResolvedSelectQueryInterface $query)
    {
        $solariumQuery = $this->getSolariumClient()->createSelect();

        //if there is a search term, set it
        if ($query->hasSearchTerm()) {
            $solariumQuery->setQuery($query->getSearchTerm());
        }

        //determine whether we are using the facet values for an underlying "base" (recorded) query
        $shouldUseFacetValuesForRecordedQuery = $query->shouldUseFacetValuesForRecordedQuery();

        //if there are filter queries, add them
        $filterQueries = $query->getFilterQueries();
        $extraLuceneFilters = [];

        if ($shouldUseFacetValuesForRecordedQuery) {
            //pick out Lucene representations for the filters being applied on top of an underlying "base" query
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
            $solariumQuery->setFields($fields);
        }

        //if there are facets to request, request them
        $facets = $query->getFacets();
        if (!empty($facets)) {
            $facetNamesToExclude = $query->getFacetNamesToExclude();

            foreach ($facets as $facet) {
                //if query indicates we should skip this facet, then skip it
                if (false !== array_search($facet->getSearchKey(), $facetNamesToExclude)) {
                    continue;
                }
                $solariumFacets = [];
                //check whether to request missing facet values
                $checkMissingFacetValues = (method_exists($this->getSearchContext(), 'shouldRequestFacetValueForMissing')) ? $this->getSearchContext()->shouldRequestFacetValueForMissing() : null;

                //if it's a range facet, create accordingly
                if ($facet instanceof RangeFacetInterface) {
                    $solariumFacets[] = $solariumQuery
                        ->getFacetSet()
                        ->createFacetRange($facet->getSearchKey())
                        ->setStart($facet->getRangesStart())
                        ->setEnd($facet->getRangesEnd())
                        ->setGap($facet->getRangeSize());
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
                            ->addExcludes(array_map(function ($key) { return sprintf('fq%u', $key); }, array_keys($extraLuceneFilters)));
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
                    $solariumFacet
                        ->setField($facet->getSearchKey());

                    //set Solr local params to exclude filter values
                    if ($query->getWhetherFacetIgnoresCurrentFilters($facet)) {
                        $solariumFacet->addExclude($facet->getName());
                    }
                }
            }
        }

        //if there are boost query fields to apply, apply them, and switch the search engine to edismax from lucene
        $boostQueryFields = $query->getBoostQueryFields();

        if (!empty($boostQueryFields)) {
            //set to using edismax
            $edismax = $solariumQuery->getEDismax();
            //set query/ query alternative to all if applicable
            if ($solariumQuery->getQuery() === self::ALL_SIGNIFIER) {
                $solariumQuery->setQuery('');
                $edismax->setQueryAlternative(self::ALL_SIGNIFIER);
            }
            //apply boosts
            $queryFields = [];
            foreach ($boostQueryFields as $boostField) {
                $queryFields[] = $boostField->getAttribute()->getSearchKey(['prefer_parsed' => true]).(($boostField->getBoostFactor() !== 1) ? ('^'.strval($boostField->getBoostFactor())) : '');
            }
            $edismax->setQueryFields(implode(' ', $queryFields));
        }

        //if there are sorts to apply, apply them
        $sortCollection = $query->getSortCollection();
        if ($sortCollection instanceof SortCollectionInterface) {
            foreach ($sortCollection as $sort) {
                $solariumQuery->addSort(
                    $sort->getFilter()->getSearchKey(),
                    ($sort->isDescending()) ? SolariumQuery::SORT_DESC : SolariumQuery::SORT_ASC
                );
            }
        }

        //if there is a spellcheck request to apply, apply it
        if ($query->getSpellcheck()) {
            $solariumSpellcheck = $solariumQuery->getSpellcheck();
            if (null !== $query->getSpellcheck()->getResultLimit()) {
                $solariumSpellcheck->setCount($query->getSpellcheck()->getResultLimit());
            }
            $solariumSpellcheck->setDictionary($query->getSpellcheck()->getDictionary());
        }

        //if configured to generate debug output, and there is a search term, request debug output
        if ($query->hasSearchTerm() and $this->provideDebugOutput) {
            $solariumQuery->getDebug(); //this switches debug on
        }

        return $solariumQuery;
    }

    /**
     * Takes a collection of filter queries, and checks there is no more than one filter query per filter name.  If multiple filter queries are found against an individual filter name, they are combined together into an intersection.
     *
     * @return array Filter\FilterQueryInterface[]
     **/
    private function dedupeFilterQueries($filterQueries)
    {
        $nameCounts = [];
        foreach ($filterQueries as $filterQuery) {
            $name = $filterQuery->getSearchKey();
            if (!isset($nameCounts[$name])) {
                $nameCounts[$name] = 1;
            } else {
                $nameCounts[$name]++;
            }
        }

        //if there are no dupes, just return the original queries
        if (array_values(array_unique($nameCounts)) == [1]) {
            return $filterQueries;
        }

        $namesToProcess = array_keys(array_filter($nameCounts, function ($v) { return $v > 1; }));
        $intersectFilterQueries = [];
        $intersectibleQueries = [];
        $filters = [];
        foreach ($filterQueries as $filterQuery) {
            if (!in_array($filterQuery->getSearchKey(), $namesToProcess)) {
                continue;
            }
            $filters[$filterQuery->getSearchKey()] = $filterQuery->getFilter();
        }
        foreach ($namesToProcess as $nameToProcess) {
            $intersectibleQueries[$nameToProcess] = [];
            foreach ($filterQueries as $filterQuery) {
                if ($filterQuery->getSearchKey() === $nameToProcess) {
                    $intersectibleQueries[$nameToProcess][] = $filterQuery;
                }
            }
        }
        foreach ($intersectibleQueries as $key => $querySet) {
            $intersectFilterValue = new Filter\IntersectionFilterValue([]);
            foreach ($querySet as $query) {
                if ($query->getFilterValue() instanceof Filter\IntersectionFilterValueInterface) {
                    foreach ($query->getFilterValue() as $filterValue) {
                        $intersectFilterValue->addFilterValue($filterValue);
                    }
                } else {
                    $intersectFilterValue->addFilterValue($query->getFilterValue());
                }
            }
            $intersectFilterQueries[] = new Filter\FilterQuery($filters[$key], $intersectFilterValue);
        }

        $dedupedFilterQueries = $intersectFilterQueries;
        foreach ($filterQueries as $filterQuery) {
            if (!in_array($filterQuery->getSearchKey(), $namesToProcess)) {
                $dedupedFilterQueries[] = $filterQuery;
            }
        }

        return $dedupedFilterQueries;
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

    /**
     * @return SolariumClient
     **/
    private function getSolariumClient()
    {
        return $this->solarium;
    }
}
