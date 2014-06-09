<?php

namespace Markup\NeedleBundle\Tests\Suggest;

use Markup\NeedleBundle\Suggest\GroupedResultParser;

class GroupedResultParserTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->parser = new GroupedResultParser();
    }

    public function testParse()
    {
        $data = array(
            'name_en_GB_s' => array(
                'matches' => 84,
                'groups' => array(
                    array(
                        'groupValue' => 'gardener',
                        'doclist' => array(
                            'numFound' => 1,
                            'start' => 0,
                            'docs' => array(
                                array(
                                    'id' => '511',
                                    'parsed_category_en_GB' => 'FOOTWEAR',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            'parsed_category_en_GB_s' => array(
                'matches' => 84,
                'groups' => array(
                    array(
                        'groupValue' => null,
                        'doclist' => array(
                            'numFound' => 84,
                            'start' => 0,
                            'docs' => array(
                                array(
                                    'id' => '23',
                                    'parsed_category_en_GB' => 'SOCKS',
                                )
                            ),
                        ),
                    ),
                ),
            ),
        );
        $results = $this->parser->parse($data);
        $this->assertCount(2, $results);
        $this->assertContainsOnlyInstancesOf('Markup\NeedleBundle\Suggest\SolrSuggestResult', $results);
    }
}
