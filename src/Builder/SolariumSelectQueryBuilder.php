<?php

namespace Markup\NeedleBundle\Builder;

use Markup\NeedleBundle\Attribute\Attribute;
use Markup\NeedleBundle\Attribute\AttributeInterface;
use Markup\NeedleBundle\Boost\BoostQueryField;
use Markup\NeedleBundle\Exception\UnformableSearchKeyException;
use Markup\NeedleBundle\Facet\RangeFacetInterface;
use Markup\NeedleBundle\Lucene\BoostLucenifier;
use Markup\NeedleBundle\Lucene\FilterQueryLucenifier;
use Markup\NeedleBundle\Lucene\SearchTermProcessor;
use Markup\NeedleBundle\Query\ResolvedSelectQueryInterface;
use Markup\NeedleBundle\Sort\DefinedSortOrder;
use Markup\NeedleBundle\Sort\SortCollectionInterface;
use Solarium\QueryType\Select\Query\Query;

/**
 * An object that can build a Solarium select query that maps a generic select search query.
 */
class SolariumSelectQueryBuilder
{
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

    private function getSolariumSearchKeyByFilterSearchKey(string $searchKey)
    {
        $solariumSearchKey = 'filter_%s';

        if (substr($searchKey, 0, 1) === '-') {
            $solariumSearchKey = '-filter_%s';
            $searchKey = substr($searchKey, 1);
        }

        return sprintf($solariumSearchKey, $searchKey);
    }

    private function getExcludeTagNameByFilterSearchKey(string $searchKey)
    {
        return sprintf('applied_filter_tag_%s', $searchKey);
    }

    /**
     * Builds, and returns, a Solarium select query that maps a generic select search query 1:1.
     *
     * @param ResolvedSelectQueryInterface $query
     * @param Query $solariumQuery
     * @return Query
     */
    public function buildSolariumQueryFromGeneric(
        ResolvedSelectQueryInterface $query,
        Query $solariumQuery
    ) {
        //if there is a search term, set it
        if ($query->hasSearchTerm()) {
            $rawTerm = $query->getSearchTerm();
            $termProcessor = new SearchTermProcessor();
            $luceneTerm = ($query->shouldUseFuzzyMatching())
                ? $termProcessor->process(
                    $rawTerm,
                    SearchTermProcessor::FILTER_NORMALIZE | SearchTermProcessor::FILTER_FUZZY_MATCHING
                )
                : $termProcessor->process($rawTerm);
            $solariumQuery->setQuery($luceneTerm);
        }

        foreach ($query->getBaseAndContextFilterQueries() as $filterQuery) {
            $searchKey = $filterQuery->getFilter()->getSearchKey();
            $solariumSearchKey = $this->getSolariumSearchKeyByFilterSearchKey($searchKey);

            $solariumQuery
                ->createFilterQuery($solariumSearchKey)
                ->setQuery($this->lucenifier->lucenify($solariumSearchKey, $filterQuery->getFilterValue()));
        }

        $allExcludeTagNames = [];

        foreach ($query->getAppliedFilterQueries() as $filterQuery) {
            $searchKey = $filterQuery->getFilter()->getSearchKey();
            $solariumSearchKey = $this->getSolariumSearchKeyByFilterSearchKey($searchKey);

            // applied filters are not set as 'exclude tags' and therefore dont affect counts in the same way
            $solariumQuery
                ->createFilterQuery($solariumSearchKey)
                ->setQuery($this->lucenifier->lucenify($solariumSearchKey, $filterQuery->getFilterValue()))
                ->addTag($this->getExcludeTagNameByFilterSearchKey($searchKey));

            $allExcludeTagNames[] = $this->getExcludeTagNameByFilterSearchKey($searchKey);
        }

        //if there are fields specified, set them
        $fields = $query->getFields();
        if (!empty($fields)) {
            $solariumQuery->setFields(array_map(
                function ($field) {
                    if ($field instanceof AttributeInterface) {
                        return $field->getSearchKey();
                    }

                    return strval($field);
                },
                $fields
            ));
        }

        //if there are facets to request, request them
        $facets = $query->getFacets();
        if (!empty($facets)) {
            $facetNamesToExclude = $query->getFacetsToExclude();

            $usingFacetComponent = false;

            foreach ($facets as $facet) {
                //if query indicates we should skip this facet, then skip it
                if (false !== array_search($facet->getSearchKey(), $facetNamesToExclude)) {
                    continue;
                }

                /**
                 * https://lucene.472066.n3.nabble.com/Case-insensitive-searches-and-facet-case-td531799.html
                 *
                 * Its expected Facets are using a facet_ key (for the following reason)
                 *
                 * Yes, use different fields.  Generally facet fields are "string" which
                 * will maintain exact case.  You can leverage the copyField capabilities
                 * in schema.xml to clone a field and analyze it differently.
                 */
                $facetSearchKey = sprintf('facet_%s', $facet->getSearchKey());

                $usingFacetComponent = true;
                //check whether to request missing facet values
                $checkMissingFacetValues = $query->shouldRequestFacetValueForMissing() ?: false;

                //if it's a range facet, create accordingly
                if ($facet instanceof RangeFacetInterface) {
                    $conf = $solariumQuery
                        ->getFacetSet()
                        ->createFacetRange($facetSearchKey)
                        ->setStart((string)$facet->getRangesStart())
                        ->setEnd((string)$facet->getRangesEnd())
                        ->setGap((string)$facet->getRangeSize())
                        ->setField($facetSearchKey);
                } else {
                    $facetSortOrder = $query->getSortOrderForFacet($facet);

                    $conf = $solariumQuery
                        ->getFacetSet()
                        ->createFacetField($facetSearchKey)
                        ->setMinCount(0)
                        ->setMissing($checkMissingFacetValues)
                        ->setSort($facetSortOrder ?: 'index')
                        ->setField($facetSearchKey);
                }

                if ($query->getWhetherFacetIgnoresCurrentFilters($facet)) {
                    $conf->addExclude($this->getExcludeTagNameByFilterSearchKey($facet->getSearchKey()));

                    // Leaving this here as an example of how you can have a setup whereby faceting only affects the
                    // result set but never adjusts the counts or values returned in available facets

                    // foreach ($allExcludeTagNames as $tagName) {
                    //  $conf->addExclude($tagName);
                    // }
                }
            }
            //by default, remove the limit on facet results
            //Solr has a default limit of 100, but a negative value removes it
            //@see https://wiki.apache.org/solr/SimpleFacetParameters#facet.limit
            if ($usingFacetComponent) {
                $solariumQuery->getFacetSet()->setLimit(-1);
            }
        }

        // set to using edismax
        // we always need to use edismax because we no longer maintain a 'text' field in the document against which
        // to perform searches
        $edismax = $solariumQuery->getEDisMax();
        $boostLucenifier = new BoostLucenifier();

        //set query/ query alternative to all if applicable
        if ($solariumQuery->getQuery() === self::ALL_SIGNIFIER) {
            $solariumQuery->setQuery('');
            $edismax->setQueryAlternative(self::ALL_SIGNIFIER);
        }

        // if a defined order of documents has been provided
        $definedSortOrder = $query->getDefinedSortOrder();
        if ($definedSortOrder instanceof DefinedSortOrder) {
            $fieldName = $definedSortOrder->getFieldName();
            $count = count($definedSortOrder);
            $parts = [];
            foreach ($definedSortOrder->getValues() as $index => $value) {
                $inverseIndex = $count - $index;
                $parts[] = $boostLucenifier->lucenifyBoost(
                    new BoostQueryField(
                        new Attribute(sprintf('%s:%s', $fieldName, $value)),
                        $inverseIndex
                    )
                );
            }
            $edismax->setBoostQuery(implode(' ', $parts));
        }

        $boostQueryFields = $query->getBoostQueryFields();

        // if searching text
        if ($query->hasSearchTerm()) {
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

        if ($sortCollection instanceof SortCollectionInterface && !$query->hasSearchTerm()) {
            foreach ($sortCollection as $sort) {
                try {
                    $sortKey = $sort->getFilter()->getSearchKey();
                } catch (UnformableSearchKeyException $e) {
                    //it's just a sort, so continue
                    continue;
                }

                $solariumQuery->addSort(
                    $sortKey,
                    ($sort->isDescending()) ? Query::SORT_DESC : Query::SORT_ASC
                );
            }
        }

        $groupingField = $query->getGroupingField();

        // if grouping on a field then group - and apply grouping sort if applicable
        if ($groupingField !== null) {
            $groupComponent = $solariumQuery->getGrouping();
            $groupComponent->addField($groupingField->getSearchKey());
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
                        ($groupingSort->isDescending()) ? Query::SORT_DESC : Query::SORT_ASC
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
}
