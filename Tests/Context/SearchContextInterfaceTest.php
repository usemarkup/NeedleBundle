<?php

namespace Markup\NeedleBundle\Tests\Context;

use Markup\NeedleBundle\Context\SearchContextInterface;
use Markup\NeedleBundle\Tests\AbstractInterfaceTestCase;

/**
 * A test for an interface for display contexts for search engines.
 */
class SearchContextInterfaceTest extends AbstractInterfaceTestCase
{
    protected function getExpectedPublicMethods()
    {
        return [
            'getItemsPerPage',
            'getFacets',
            'getDefaultFilterQueries',
            'getDefaultSortCollectionForQuery',
            'getSetDecoratorForFacet',
            'getWhetherFacetIgnoresCurrentFilters',
            'getAvailableFilterNames',
            'getBoostQueryFields',
            'getFacetCollatorProvider',
            'getFacetSortOrderProvider',
            'getInterceptor',
            'shouldRequestFacetValueForMissing',
            'shouldUseFuzzyMatching',
        ];
    }

    protected function getInterfaceUnderTest()
    {
        return SearchContextInterface::class;
    }
}
