<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Crontab;

use Hyperf\Crontab\Scheduler as BaseScheduler;

class Scheduler extends BaseScheduler
{
    protected function getSchedules(): array
    {
        $schedulers = $this->crontabManager->parse();
        $result = [];
        foreach ($schedulers as $schedule) {
            if ($schedule->getExecuteTime()->isCurrentMinute()) {
                $result[] = $schedule;
            }
        }
        return $result;
    }
}
