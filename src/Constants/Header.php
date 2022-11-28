<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Constants;

class Header
{
    public const X_USER_ID = 'X-User-ID';

    public const X_CONSUMER_NAME = 'X-Consumer-Name';

    public const X_APP = 'X-App';

    public const X_CLIENT_PLATFORM = 'X-Client-Platform';

    public const X_CLIENT_VERSION = 'X-Client-Version';

    public const X_CLIENT_VERSION_CODE = 'X-Client-Version-Code';

    public const X_CLIENT_PACKAGE = 'X-Client-Package';

    public const X_CLIENT_APP_NAME = 'X-Client-App-Name';

    public const X_CLIENT_DEVICE_ID = 'X-Client-Device-Id';

    public const X_CLIENT_CHANNEL = 'X-Client-Channel';

    public const X_REAL_IP = 'X-Real-Ip';

    public const X_CLIENT_REFER = 'X-Client-Refer';

    public const X_REQUEST_ID = 'X-Request-Id';

    public const X_LANGUAGE = 'X-Language';

    public const X_B3_TRACE_ID = 'X-B3-TraceId';

    public const X_B3_PARENT_SPAN_ID = 'X-B3-ParentSpanId';

    public const X_B3_SPAN_ID = 'X-B3-SpanId';

    public const X_B3_SAMPLED = 'X-B3-Sampled';

    public const X_B3_FLAGS = 'X-B3-Flags';

    public const X_OT_SPAN_CONTEXT = 'x-ot-span-context';

    public const B3 = 'b3';

    public static function getAllHeaderKeys(): array
    {
        $reflection = new \ReflectionClass(__CLASS__);
        return array_values($reflection->getConstants());
    }
}
