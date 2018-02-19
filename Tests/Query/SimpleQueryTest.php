<?php

namespace Markup\NeedleBundle\Tests\Query;

use Markup\NeedleBundle\Query\SimpleQuery;
use Markup\NeedleBundle\Query\SimpleQueryInterface;
use PHPUnit\Framework\TestCase;

/**
* Test for a simple query implementation.
*/
class SimpleQueryTest extends TestCase
{
    public function testIsSimpleQuery()
    {
        $query = new SimpleQuery();
        $this->assertInstanceOf(SimpleQueryInterface::class, $query);
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
