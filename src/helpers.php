<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
use Tusimo\Resource\Entity\RequestContext;

function container(): Psr\Container\ContainerInterface
{
    return \Hyperf\Utils\ApplicationContext::getContainer();
}

function has_container(): bool
{
    return \Hyperf\Utils\ApplicationContext::hasContainer();
}

function is_production(): bool
{
    return environment() === 'production' || environment() === 'product';
}

function is_pre(): bool
{
    return environment() === 'pre';
}

function is_test(): bool
{
    return environment() === 'test';
}
function is_development(): bool
{
    return environment() === 'development';
}
function is_local(): bool
{
    return environment() === 'local';
}

function is_testing(): bool
{
    return environment() === 'testing';
}

function environment(): string
{
    return env('APP_ENV', 'production');
}

function request(): Tusimo\Resource\Entity\ResourceRequest
{
    return new Tusimo\Resource\Entity\ResourceRequest();
}

function response(): Tusimo\Resource\Entity\ResourceResponse
{
    return new Tusimo\Resource\Entity\ResourceResponse();
}

function request_context(): RequestContext
{
    $context = RequestContext::getRequestContext();
    if (is_null($context)) {
        $context = RequestContext::createFromArray([]);
    }
    return $context;
}

function formatBytes($size, $precision = 2)
{
    $base = log($size, 1024);
    $suffixes = ['', 'K', 'M', 'G', 'T'];

    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
}

function is_empty_dir(string $path): bool
{
    $handler = @opendir($path);
    $i = 0;
    while ($file = readdir($handler)) {
        ++$i;
    }
    closedir($handler);
    if ($i > 2) {
        return false;
    }
    return true;
}
