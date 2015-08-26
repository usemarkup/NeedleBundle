<?php

namespace Markup\NeedleBundle\Suggest;

/**
 * An object that can parse out the return from a suggest query that groups results into expected objects.
 */
class GroupedResultParser
{
    /**
     * @param array $data
     * @return SolrSuggestResult[]
     */
    public function parse(array $data)
    {
        $results = [];
        foreach ($data as $key => $resultData) {
            $results[$key] = new SolrSuggestResult($resultData);
        }

        return $results;
    }
} 
