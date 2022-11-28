<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Test\Cases;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class HelperTest extends TestCase
{
    public function testBytesFormat()
    {
        $format = formatBytes(24962496);
        $this->assertEquals('23.81 M', $format);
        $format = formatBytes(24962496, 0);
        $this->assertEquals('24 M', $format);
        $format = formatBytes(24962496, 4);
        $this->assertEquals('23.8061 M', $format);
    }
}
