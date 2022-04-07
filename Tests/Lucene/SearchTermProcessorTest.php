<?php

namespace Markup\NeedleBundle\Tests\Lucene;

use Markup\NeedleBundle\Lucene\SearchTermProcessor;
use PHPUnit\Framework\TestCase;

class SearchTermProcessorTest extends TestCase
{
    /**
     * @var SearchTermProcessor
     */
    private $processor;

    protected function setUp()
    {
        $this->processor = new SearchTermProcessor();
    }

    public function testProcessWithNoTransformSetPerformsNoTransform()
    {
        $text = 'ghost+ "joker~ hundred"';
        $this->assertEquals($text, $this->processor->process($text, SearchTermProcessor::FILTER_NONE));
    }

    /**
     * @dataProvider normalizeCases
     */
    public function testProcessWithNormalization(string $input, string $output)
    {
        $this->assertEquals(
            $output,
            $this->processor->process($input, SearchTermProcessor::FILTER_NORMALIZE)
        );
    }

    /**
     * @dataProvider normalizeCases
     */
    public function testProcessesWithNormalizationByDefault(string $input, string $output)
    {
        $this->assertEquals(
            $output,
            $this->processor->process($input)
        );
    }

    public function normalizeCases()
    {
        return [
            ['red:case', 'red\:case'],
            ['red~ cane~', 'red cane'],
            ['"red cane"', '"red cane"'],
            ['"red cane"~', '"red cane"'],
            ['red  handle', 'red handle'],
            ['röda  bilar', 'röda bilar'],
        ];
    }

    /**
     * @dataProvider normalizeAndFuzzyMatchCases
     */
    public function testProcessesWithNormalizationAndFuzzyMatching(string $input, string $output)
    {
        $this->assertEquals(
            $output,
            $this->processor->process(
                $input,
                SearchTermProcessor::FILTER_NORMALIZE | SearchTermProcessor::FILTER_FUZZY_MATCHING
            )
        );
    }

    public function normalizeAndFuzzyMatchCases()
    {
        return [
            ['blue shirts', 'blue~ shirts~'],
            ['"blue shirts"', '"blue shirts"~'],
            ['blue~ shirts~', 'blue~ shirts~'],
            ['"blue shirts"~', '"blue shirts"~'],
            ['"blåa skjortor"~', '"blåa skjortor"~'],
            ['blåa skjortor', 'blåa~ skjortor~'],
        ];
    }
}
