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
        $data = [
            'name_en_GB_s' => [
                'matches' => 84,
                'groups' => [
                    [
                        'groupValue' => 'gardener',
                        'doclist' => [
                            'numFound' => 1,
                            'start' => 0,
                            'docs' => [
                                [
                                    'id' => '511',
                                    'parsed_category_en_GB' => 'FOOTWEAR',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'parsed_category_en_GB_s' => [
                'matches' => 84,
                'groups' => [
                    [
                        'groupValue' => null,
                        'doclist' => [
                            'numFound' => 84,
                            'start' => 0,
                            'docs' => [
                                [
                                    'id' => '23',
                                    'parsed_category_en_GB' => 'SOCKS',
                                ]
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $results = $this->parser->parse($data);
        $this->assertCount(2, $results);
        $this->assertContainsOnlyInstancesOf('Markup\NeedleBundle\Suggest\SolrSuggestResult', $results);
    }
}
