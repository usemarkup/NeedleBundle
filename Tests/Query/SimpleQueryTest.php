<?php

namespace Markup\NeedleBundle\Tests\Query;

use Markup\NeedleBundle\Query\SimpleQuery;

/**
* Test for a simple query implementation.
*/
class SimpleQueryTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSimpleQuery()
    {
        $query = new SimpleQuery();
        $this->assertInstanceOf('Markup\NeedleBundle\Query\SimpleQueryInterface', $query);
    }

    public function testNonEmptyQuery()
    {
        $term = 'dogs';
        $query = new SimpleQuery($term);
        $this->assertEquals($term, $query->getSearchTerm());
        $this->assertTrue($query->hasSearchTerm());
    }

    public function testEmptyQuery()
    {
        $query = new SimpleQuery();
        $this->assertEquals('', $query->getSearchTerm());
        $this->assertFalse($query->hasSearchTerm());
    }
}
