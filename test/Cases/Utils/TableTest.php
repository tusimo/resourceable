<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Test\Cases;

use PHPUnit\Framework\TestCase;
use Tusimo\Resource\Utils\SwooleTableManager;

/**
 * @internal
 * @coversNothing
 */
class TableTest extends TestCase
{
    public function testTable()
    {
        $table = SwooleTableManager::getTable('test', 64, 100);

        for ($i = 0; $i < 64; ++$i) {
            $table->set($i . '', ['id' => $i, 'name' => 'test' . $i]);
        }
        $this->assertEquals(64, $table->count());
        $table->set('4455', ['id' => 44, 'name' => 'test44']);
        $this->assertEquals(ceil(64 * 0.8), $table->count());

        $table->del('4455');

        $this->assertEquals(false, $table->get('4455'));
    }

    public function testTableExpire()
    {
        $table = SwooleTableManager::getTable('test2', 64, 100);

        for ($i = 0; $i < 64; ++$i) {
            $table->setWithExpire($i . '', ['id' => $i, 'name' => 'test' . $i], 1);
        }
        $this->assertEquals(64, $table->count());
        $table->set('4455', ['id' => 44, 'name' => 'test44']);
        $this->assertEquals(ceil(64 * 0.8), $table->count());

        $table->del('4455');

        $this->assertEquals(false, $table->get('4455'));
        sleep(2);
        for ($i = 0; $i < 64; ++$i) {
            $this->assertEquals(false, $table->get($i . ''));
        }
        $this->assertEquals(0, $table->count());

        for ($i = 0; $i < 128; ++$i) {
            $table->setWithExpire($i . '', ['id' => $i, 'name' => 'test' . $i], 2);
        }
        $this->assertEquals(63, $table->count());
        $table->setWithExpire('test', null, 2);
        $this->assertEquals(null, $table->get('test'));
    }

    public function testTableSet()
    {
        $table = SwooleTableManager::getTable('test3', 64, 100);

        for ($i = 0; $i < 1000; ++$i) {
            $table->setWithExpire($i . '', ['id' => $i, 'name' => 'test' . $i], 2);
            $this->assertEquals(['id' => $i, 'name' => 'test' . $i], $table->get($i . ''));
        }
    }
}
