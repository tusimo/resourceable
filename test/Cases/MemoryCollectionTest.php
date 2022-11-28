<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Test\Cases;

use PHPUnit\Framework\TestCase;
use Tusimo\Resource\Utils\MemoryCollection;

/**
 * @internal
 * @coversNothing
 */
class MemoryCollectionTest extends TestCase
{
    public function testSelect()
    {
        $collection = new MemoryCollection([
            [
                'id' => 1,
                'age' => 21,
                'name' => 'foo',
            ],
            [
                'id' => 2,
                'age' => 20,
                'name' => 'bar',
            ],
            [
                'id' => 3,
                'age' => 25,
                'name' => 'joe',
            ],
        ]);
        $selectedCollection = $collection->select(['id', 'age']);
        foreach ($selectedCollection as $item) {
            $this->assertArrayHasKey('id', $item);
            $this->assertArrayHasKey('age', $item);
            $this->assertArrayNotHasKey('name', $item);
        }
        $selectedCollection = $collection->select(['*']);
        foreach ($selectedCollection as $item) {
            $this->assertArrayHasKey('id', $item);
            $this->assertArrayHasKey('age', $item);
            $this->assertArrayHasKey('name', $item);
        }
        $selectedCollection = $collection->select(['id', '*']);
        foreach ($selectedCollection as $item) {
            $this->assertArrayHasKey('id', $item);
            $this->assertArrayHasKey('age', $item);
            $this->assertArrayHasKey('name', $item);
        }
        $selectedCollection = $collection->select([]);
        foreach ($selectedCollection as $item) {
            $this->assertArrayHasKey('id', $item);
            $this->assertArrayHasKey('age', $item);
            $this->assertArrayHasKey('name', $item);
        }
    }

    public function testOffset()
    {
        $collection = new MemoryCollection([
            [
                'id' => 1,
                'age' => 21,
                'name' => 'foo',
            ],
            [
                'id' => 2,
                'age' => 20,
                'name' => 'bar',
            ],
            [
                'id' => 3,
                'age' => 25,
                'name' => 'joe',
            ],
        ]);
        $offsetCollection = $collection->offset(1);
        $this->assertEquals(2, $offsetCollection->count());
        $this->assertEquals($offsetCollection->all(), $collection->slice(1)->all());
        $offsetCollection = $collection->offset(2);
        $this->assertEquals(1, $offsetCollection->count());
        $this->assertEquals($offsetCollection->all(), $collection->slice(2)->all());
    }

    public function testLimit()
    {
        $collection = new MemoryCollection([
            [
                'id' => 1,
                'age' => 21,
                'name' => 'foo',
            ],
            [
                'id' => 2,
                'age' => 20,
                'name' => 'bar',
            ],
            [
                'id' => 3,
                'age' => 25,
                'name' => 'joe',
            ],
        ]);
        $offsetCollection = $collection->limit(1);
        $this->assertEquals(1, $offsetCollection->count());
        $this->assertEquals($offsetCollection->all(), $collection->slice(0, 1)->all());
        $offsetCollection = $collection->limit(2);
        $this->assertEquals(2, $offsetCollection->count());
        $this->assertEquals($offsetCollection->all(), $collection->slice(0, 2)->all());
    }

    public function testOrderBy()
    {
        $collection = new MemoryCollection([
            [
                'id' => 1,
                'age' => 21,
                'name' => 'foo',
            ],
            [
                'id' => 2,
                'age' => 20,
                'name' => 'bar',
            ],
            [
                'id' => 3,
                'age' => 25,
                'name' => 'joe',
            ],
        ]);
        $orderedCollection = $collection->orderBy('age', 'desc');
        $this->assertEquals(3, $orderedCollection->count());
        $this->assertEquals($orderedCollection->first(), $collection->last());
        $orderedCollection = $collection->orderBy('age', 'asc');
        $this->assertEquals(3, $orderedCollection->count());

        $this->assertEquals($orderedCollection->first(), $collection->offset(1)->first());
    }

    public function testWhereBetween()
    {
        $collection = new MemoryCollection([
            [
                'id' => 1,
                'age' => 21,
                'name' => 'foo',
            ],
            [
                'id' => 2,
                'age' => 20,
                'name' => 'bar',
            ],
            [
                'id' => 3,
                'age' => 25,
                'name' => 'joe',
            ],
        ]);
        $whereCollection = $collection->whereBetween('age', [15, 20]);
        $this->assertEquals(1, $whereCollection->count());
        $this->assertEquals($collection->get(1), $whereCollection->first());
    }
}
