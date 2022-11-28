<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Entity;

use Hyperf\Utils\Str;
use Tusimo\Query\Query;
use Hyperf\Utils\Context;
use Hyperf\HttpServer\Request;
use Psr\Http\Message\ServerRequestInterface;

class ResourceRequest extends Request
{
    use RequestHeaderTrait;

    protected string $routeResourceName = 'resource_id';

    /**
     * Is Batch api or not.
     */
    public function isBatch(): bool
    {
        if (Str::endsWith($this->getUri()->getPath(), '_batch')) {
            return true;
        }
        if (! in_array($this->getMethod(), $this->multiResourceMethods())) {
            return false;
        }
        if (Str::contains($this->route('resource_id', ''), ',')) {
            return true;
        }
        return false;
    }

    /**
     * Get Resource from request.
     */
    public function getResource(): array
    {
        if ($this->isBatch()) {
            return [];
        }
        return $this->getParsedBody();
    }

    /**
     * Return all resource.
     */
    public function getResources(): array
    {
        if (! $this->isBatch()) {
            return [];
        }
        return $this->getParsedBody();
    }

    public function getQuery(): Query
    {
        return query()->fromUriQueryString($this->getUri()->getQuery() ?? '');
    }

    public function getWith(): array
    {
        $queryWith = $this->getQuery()->getQueryWith();
        if (is_null($queryWith)) {
            return [];
        }
        return $queryWith->getWith();
    }

    public function getSelect(): array
    {
        $querySelect = $this->getQuery()->getQuerySelect();
        if (is_null($querySelect)) {
            return [];
        }
        return $querySelect->getSelects();
    }

    public function getResourceId(): string
    {
        return $this->getRouteResourceId();
    }

    /**
     * Get ResourceId from route.
     *
     * @return array|string
     */
    public function getRouteResourceId(): mixed
    {
        $resourceIdString = $this->route($this->routeResourceName);

        if ($this->isBatch()) {
            return explode(',', $resourceIdString);
        }
        return $resourceIdString;
    }

    public function getRequestContext(): RequestContext
    {
        return RequestContext::createFromRequest($this);
    }

    public function hasRequest(): bool
    {
        return Context::get(ServerRequestInterface::class) !== null;
    }

    protected function multiResourceMethods(): array
    {
        return ['GET', 'DELETE'];
    }
}
