<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Test\Cases\Cron;

use PHPUnit\Framework\TestCase;
use Tusimo\Test\Cron\HelloWorld;

/**
 * @internal
 * @coversNothing
 */
class CronExpressionTest extends TestCase
{
    public function testFunctions()
    {
        $cron = new HelloWorld();
        $this->assertEquals($cron->everySeconds()->getRule(), '* * * * * *');
        $cron = new HelloWorld();
        $this->assertEquals($cron->everyTwoSeconds()->getRule(), '*/2 * * * * *');
        $cron = new HelloWorld();
        $this->assertEquals($cron->everyMinutes()->getRule(), '0 * * * * *');
        $cron = new HelloWorld();
        $cron->everyTenSeconds()->everyTwoHours()->everyMinutes();
        $this->assertEquals($cron->getRule(), '*/10 * */2 * * *');
        $cron = new HelloWorld();
        $cron->hourlyAt(10);
        $this->assertEquals($cron->getRule(), '0 10 * * * *');
        $cron = new HelloWorld();
        $cron->daily();
        $this->assertEquals($cron->getRule(), '0 0 0 * * *');
        $cron = new HelloWorld();
        $cron->dailyAt('12:40');
        $this->assertEquals($cron->getRule(), '0 40 12 * * *');
        $cron = new HelloWorld();
        $cron->dailyAt('12:40')->hourlyAt(30);
        $this->assertEquals($cron->getRule(), '0 30 12 * * *');
        $cron = new HelloWorld();
        $cron->dailyAt('12:40')->hourlyAt(30)->saturdays();
        $this->assertEquals($cron->getRule(), '0 30 12 * * 6');
    }
}
