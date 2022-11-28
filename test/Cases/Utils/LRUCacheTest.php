<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Test\Cases;

use PHPUnit\Framework\TestCase;
use Tusimo\Resource\Utils\LRUCache;

/**
 * @internal
 * @coversNothing
 */
class LRUCacheTest extends TestCase
{
    public function testLRUCache()
    {
        $cache = new LRUCache(3);
        $cache->put('a', 'a');
        $cache->put('b', 'b');
        $cache->put('c', 'c');
        $cache->put('d', 'd');
        $this->assertEquals(null, $cache->get('a'));
        $this->assertEquals('b', $cache->get('b'));
        $this->assertEquals('c', $cache->get('c'));
        $this->assertEquals('d', $cache->get('d'));
        $cache->put('d', 'd');
        $this->assertEquals(null, $cache->get('a'));
        $this->assertEquals('b', $cache->get('b'));
        $this->assertEquals('c', $cache->get('c'));
        $this->assertEquals('d', $cache->get('d'));
        $cache->put('e', 'e');
        $this->assertEquals(null, $cache->get('a'));
        $this->assertEquals(null, $cache->get('b'));
        $this->assertEquals('c', $cache->get('c'));
        $this->assertEquals('d', $cache->get('d'));
        $this->assertEquals('e', $cache->get('e'));
        $cache->put('f', 'f');
        $this->assertEquals(null, $cache->get('a'));
        $this->assertEquals(null, $cache->get('b'));
        $this->assertEquals(null, $cache->get('c'));
        $this->assertEquals('d', $cache->get('d'));
        $this->assertEquals('e', $cache->get('e'));
        $this->assertEquals('f', $cache->get('f'));
        $cache->put('a', 'a');
        $cache->put('c', 'c');
        $this->assertEquals(null, $cache->get('b'));
        $this->assertEquals(null, $cache->get('d'));
        $this->assertEquals(null, $cache->get('e'));
        $this->assertEquals('f', $cache->get('f'));
        $this->assertEquals('a', $cache->get('a'));
        $this->assertEquals('c', $cache->get('c'));
        $this->assertEquals(3, $cache->capacity());
    }
}
