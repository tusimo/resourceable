<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Crontab;

use Carbon\Carbon;
use Hyperf\Crontab\Crontab;

abstract class Cron extends Crontab
{
    public const SUNDAY = 0;

    public const MONDAY = 1;

    public const TUESDAY = 2;

    public const WEDNESDAY = 3;

    public const THURSDAY = 4;

    public const FRIDAY = 5;

    public const SATURDAY = 6;

    /**
     * @var bool
     */
    protected $onOneServer = true;

    protected $rule = '0 * * * * *';

    abstract public function execute();

    public function getCallback()
    {
        return [static::class, 'execute'];
    }

    public function everySeconds()
    {
        return $this->spliceIntoPosition(1, '*');
    }

    public function everyTwoSeconds()
    {
        return $this->spliceIntoPosition(1, '*/2');
    }

    public function everyFiveSeconds()
    {
        return $this->spliceIntoPosition(1, '*/5');
    }

    public function everyTenSeconds()
    {
        return $this->spliceIntoPosition(1, '*/10');
    }

    public function everyThirtySeconds()
    {
        return $this->spliceIntoPosition(1, '*/30');
    }

    public function everyMinutes()
    {
        return $this->spliceIntoPosition(2, '*');
    }

    public function everyTwoMinutes()
    {
        return $this->spliceIntoPosition(2, '*/2');
    }

    public function everyFiveMinutes()
    {
        return $this->spliceIntoPosition(2, '*/5');
    }

    public function everyTenMinutes()
    {
        return $this->spliceIntoPosition(2, '*/10');
    }

    public function everyThirtyMinutes()
    {
        return $this->spliceIntoPosition(2, '*/30');
    }

    public function hourly()
    {
        return $this->spliceIntoPosition(2, '0');
    }

    /**
     * Schedule the event to run hourly at a given offset in the hour.
     *
     * @param array|int $offset
     * @return $this
     */
    public function hourlyAt($offset)
    {
        $offset = is_array($offset) ? implode(',', $offset) : $offset;

        return $this->spliceIntoPosition(2, $offset);
    }

    /**
     * Schedule the event to run every two hours.
     *
     * @return $this
     */
    public function everyTwoHours()
    {
        return $this->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(3, '*/2');
    }

    /**
     * Schedule the event to run every three hours.
     *
     * @return $this
     */
    public function everyThreeHours()
    {
        return $this->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(3, '*/3');
    }

    /**
     * Schedule the event to run every four hours.
     *
     * @return $this
     */
    public function everyFourHours()
    {
        return $this->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(3, '*/4');
    }

    /**
     * Schedule the event to run every five hours.
     *
     * @return $this
     */
    public function everyFiveHours()
    {
        return $this->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(3, '*/5');
    }

    /**
     * Schedule the event to run every six hours.
     *
     * @return $this
     */
    public function everySixHours()
    {
        return $this->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(3, '*/6');
    }

    public function daily()
    {
        return $this->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(3, 0);
    }

    /**
     * Schedule the event to run daily at a given time.
     *
     * @param string $time
     * @return $this
     */
    public function dailyAt($time)
    {
        $segments = explode(':', $time);

        return $this->spliceIntoPosition(3, (int) $segments[0])
            ->spliceIntoPosition(2, count($segments) === 2 ? (int) $segments[1] : '0');
    }

    /**
     * Schedule the event to run twice daily.
     *
     * @param int $first
     * @param int $second
     * @return $this
     */
    public function twiceDaily($first = 1, $second = 13)
    {
        return $this->twiceDailyAt($first, $second, 0);
    }

    /**
     * Schedule the event to run twice daily at a given offset.
     *
     * @param int $first
     * @param int $second
     * @param int $offset
     * @return $this
     */
    public function twiceDailyAt($first = 1, $second = 13, $offset = 0)
    {
        $hours = $first . ',' . $second;

        return $this->spliceIntoPosition(2, $offset)
            ->spliceIntoPosition(3, $hours);
    }

    /**
     * Schedule the event to run only on weekdays.
     *
     * @return $this
     */
    public function weekdays()
    {
        return $this->days(static::MONDAY . '-' . static::FRIDAY);
    }

    /**
     * Schedule the event to run only on weekends.
     *
     * @return $this
     */
    public function weekends()
    {
        return $this->days(static::SATURDAY . ',' . static::SUNDAY);
    }

    /**
     * Schedule the event to run only on Mondays.
     *
     * @return $this
     */
    public function mondays()
    {
        return $this->days(static::MONDAY);
    }

    /**
     * Schedule the event to run only on Tuesdays.
     *
     * @return $this
     */
    public function tuesdays()
    {
        return $this->days(static::TUESDAY);
    }

    /**
     * Schedule the event to run only on Wednesdays.
     *
     * @return $this
     */
    public function wednesdays()
    {
        return $this->days(static::WEDNESDAY);
    }

    /**
     * Schedule the event to run only on Thursdays.
     *
     * @return $this
     */
    public function thursdays()
    {
        return $this->days(static::THURSDAY);
    }

    /**
     * Schedule the event to run only on Fridays.
     *
     * @return $this
     */
    public function fridays()
    {
        return $this->days(static::FRIDAY);
    }

    /**
     * Schedule the event to run only on Saturdays.
     *
     * @return $this
     */
    public function saturdays()
    {
        return $this->days(static::SATURDAY);
    }

    /**
     * Schedule the event to run only on Sundays.
     *
     * @return $this
     */
    public function sundays()
    {
        return $this->days(static::SUNDAY);
    }

    /**
     * Schedule the event to run weekly.
     *
     * @return $this
     */
    public function weekly()
    {
        return $this->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(3, 0)
            ->spliceIntoPosition(6, 0);
    }

    /**
     * Schedule the event to run weekly on a given day and time.
     *
     * @param array|mixed $dayOfWeek
     * @param string $time
     * @return $this
     */
    public function weeklyOn($dayOfWeek, $time = '0:0')
    {
        $this->dailyAt($time);

        return $this->days($dayOfWeek);
    }

    /**
     * Schedule the event to run monthly.
     *
     * @return $this
     */
    public function monthly()
    {
        return $this->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(3, 0)
            ->spliceIntoPosition(4, 1);
    }

    /**
     * Schedule the event to run monthly on a given day and time.
     *
     * @param int $dayOfMonth
     * @param string $time
     * @return $this
     */
    public function monthlyOn($dayOfMonth = 1, $time = '0:0')
    {
        $this->dailyAt($time);

        return $this->spliceIntoPosition(4, $dayOfMonth);
    }

    /**
     * Schedule the event to run twice monthly at a given time.
     *
     * @param int $first
     * @param int $second
     * @param string $time
     * @return $this
     */
    public function twiceMonthly($first = 1, $second = 16, $time = '0:0')
    {
        $daysOfMonth = $first . ',' . $second;

        $this->dailyAt($time);

        return $this->spliceIntoPosition(4, $daysOfMonth);
    }

    /**
     * Schedule the event to run on the last day of the month.
     *
     * @param string $time
     * @return $this
     */
    public function lastDayOfMonth($time = '0:0')
    {
        $this->dailyAt($time);

        return $this->spliceIntoPosition(4, Carbon::now()->endOfMonth()->day);
    }

    /**
     * Schedule the event to run quarterly.
     *
     * @return $this
     */
    public function quarterly()
    {
        return $this->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(3, 0)
            ->spliceIntoPosition(4, 1)
            ->spliceIntoPosition(5, '1-12/3');
    }

    /**
     * Schedule the event to run quarterly on a given day and time.
     *
     * @param int $dayOfQuarter
     * @param int $time
     * @return $this
     */
    public function quarterlyOn($dayOfQuarter = 1, $time = '0:0')
    {
        $this->dailyAt($time);

        return $this->spliceIntoPosition(4, $dayOfQuarter)
            ->spliceIntoPosition(5, '1-12/3');
    }

    /**
     * Schedule the event to run yearly.
     *
     * @return $this
     */
    public function yearly()
    {
        return $this->spliceIntoPosition(2, 0)
            ->spliceIntoPosition(3, 0)
            ->spliceIntoPosition(4, 1)
            ->spliceIntoPosition(5, 1);
    }

    /**
     * Set the days of the week the command should run on.
     *
     * @param array|mixed $days
     * @return $this
     */
    public function days($days)
    {
        $days = is_array($days) ? $days : func_get_args();

        return $this->spliceIntoPosition(6, implode(',', $days));
    }

    protected function spliceIntoPosition($position, $value)
    {
        $segments = explode(' ', $this->rule);

        $segments[$position - 1] = $value;

        $this->setRule(implode(' ', $segments));
        return $this;
    }
}
