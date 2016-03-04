<?php

namespace Markup\NeedleBundle\Adapter;

/**
 * Wraps a very specific solrarium result format associated with the GroupingComponent in
 * a way that makes it easy for the GroupedQuerySolariumAdapter to use. Will use the first document in each
 * group as the 'document' and then embed _within_ that document a property called 'groups' which will contain the whole
 * group of results (including the initial document itself). This provides a result that can be treated in the same
 * way as a non grouped result
 */
class GroupedResultAdapter
{
    /**
     * @vay array
     */
    private $result;

    /**
     * @param array $result
     */
    public function __construct($group)
    {
        $this->result = $group;
    }

    public function getResultSet()
    {
        // iterate and wrangle formats
        $this->result->getGrouping();
    }

    public function getGroupCount()
    {
        $groups = $this->result->getGrouping();

        numberOfGroups

    }
}

Result {#3259 ▼
    #groups: array:1 [▼
    "style_code" => FieldGroup {#3253 ▼
        #matches: 14
        #numberOfGroups: 7
        #valueGroups: array:7 [▶]
    }
  ]
}