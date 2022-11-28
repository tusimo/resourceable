<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Test\Cron;

use Tusimo\Resource\Crontab\Cron;

class HelloWorld extends Cron
{
    public function execute()
    {
        echo 'Hello World!';
    }
}
