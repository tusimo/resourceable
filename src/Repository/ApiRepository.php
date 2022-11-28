<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Repository;

use Tusimo\Restable\Query;
use Psr\Log\LoggerInterface;
use Tusimo\Restable\QuerySelect;
use Psr\Http\Message\ResponseInterface;
use Hyperf\Paginator\LengthAwarePaginator;
use Tusimo\Resource\Concerns\HasClientResolvable;
use Tusimo\Resource\Resolver\DefaultHeaderResolver;
use Tusimo\Resource\Contract\ClientResolverContract;
use Tusimo\Resource\Contract\HeaderResolverContract;

class ApiRepository extends Repository implements ClientResolverContract
{
    use HasClientResolvable;

    protected string $resourceName;

    protected string $baseUri = '';

    protected string $version = 'v2';

    protected string $apiVersion = 'v2';

    protected ?HeaderResolverContract $headerResolver;

    /**
     * Undocumented variable.
     *
     * @var LoggerInterface
     */
    protected ?LoggerInterface $logger;

    public function __construct(
        string $baseUri,
        string $resourceName = '',
        string $version = 'v2',
        ?HeaderResolverContract $headerResolver = null,
        ?ClientResolverContract $clientResolver = null,
        ?LoggerInterface $logger = null
    ) {
        $this->baseUri = $baseUri;
        $this->resourceName = $resourceName;
        $this->version = $version;
        $this->setHeaderResolver($headerResolver);
        $this->setClientResolver($clientResolver);
        $this->logger = $logger;
    }

    /**
     * Get the value of uri.
     */
    public function getBaseUri()
    {
        return $this->baseUri;
    }

    /**
     * Set the value of uri.
     *
     * @param mixed $baseUri
     * @return self
     */
    public function setBaseUri($baseUri)
    {
        $this->baseUri = $baseUri;

        return $this;
    }

    /**
     * Get the value of version.
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set the value of version.
     *
     * @param mixed $version
     * @return self
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get the value of commonHeaders.
     */
    public function getCommonHeaders(): array
    {
        return $this->getHeaderResolver()->toHeaders();
    }

    /**
     * Get Resource by id.
     *
     * @param int|string $id
     */
    public function get($id, array $select = [], array $with = []): array
    {
        $options = $this->getClientRequestOptions();
        $queryUri = query()->select($this->getParsedSelect($select))->with($with)->toUriQueryString($this->getVersion());
        $uri = "/api/{$this->getApiVersion()}/{$this->getResourceName()}/{$id}?" . $queryUri;

        $response = $this->request('GET', $uri, $options);
        if (is_null($response)) {
            return [];
        }
        $data = $this->decodeResponse($response);

        return $data['data'];
    }

    /**
     * Get Resources by Ids.
     */
    public function getByIds(array $ids, array $select = [], array $with = []): array
    {
        $this->throwExceptionIfNotSupported();

        $options = $this->getClientRequestOptions();
        $idsString = implode(',', $ids);
        $queryUri = query()->select($this->getParsedSelect($select))->with($with)->toUriQueryString($this->getVersion());

        $uri = "/api/{$this->getApiVersion()}/{$this->getResourceName()}/{$idsString}/_batch?" . $queryUri;

        $response = $this->request('GET', $uri, $options);
        if (is_null($response)) {
            return [];
        }
        $data = $this->decodeResponse($response);
        return $data['data'];
    }

    /**
     * Add Resource and return Resource With Id.
     */
    public function add(array $resource): array
    {
        $options = $this->getClientRequestOptions();
        $uri = "/api/{$this->getApiVersion()}/{$this->getResourceName()}";
        $options['json'] = $resource;
        $response = $this->request('POST', $uri, $options);
        if (is_null($response)) {
            return [];
        }
        $data = $this->decodeResponse($response);
        return $data['data'];
    }

    /**
     * Batch add resource.
     */
    public function batchAdd(array $resources): array
    {
        $this->throwExceptionIfNotSupported();

        $options = $this->getClientRequestOptions();
        $uri = "/api/{$this->getApiVersion()}/{$this->getResourceName()}/_batch";
        $options['json'] = $resources;
        $response = $this->request('POST', $uri, $options);
        if (is_null($response)) {
            return [];
        }
        $data = $this->decodeResponse($response);
        return $data['data'];
    }

    /**
     * Update Resource.
     *
     * @param int|string $id
     */
    public function update($id, array $resource): array
    {
        $options = $this->getClientRequestOptions();
        $uri = "/api/{$this->getApiVersion()}/{$this->getResourceName()}/{$id}";
        $options['json'] = $resource;
        $response = $this->request('PUT', $uri, $options);
        if (is_null($response)) {
            return [];
        }
        $data = $this->decodeResponse($response);
        return $data['data'];
    }

    /**
     * Batch Update Resource.
     */
    public function batchUpdate(array $resources): array
    {
        $this->throwExceptionIfNotSupported();

        $options = $this->getClientRequestOptions();
        $uri = "/api/{$this->getApiVersion()}/{$this->getResourceName()}/_batch";
        $options['json'] = $resources;
        $response = $this->request('PUT', $uri, $options);
        if (is_null($response)) {
            return [];
        }
        $data = $this->decodeResponse($response);
        return $data['data'];
    }

    /**
     * Delete resource.
     *
     * @param int|string $id
     */
    public function delete($id): bool
    {
        $options = $this->getClientRequestOptions();
        $uri = "/api/{$this->getApiVersion()}/{$this->getResourceName()}/{$id}";
        $response = $this->request('DELETE', $uri, $options);
        if (is_null($response)) {
            return false;
        }
        return true;
    }

    /**
     * Batch delete Resource.
     */
    public function deleteByIds(array $ids): int
    {
        $this->throwExceptionIfNotSupported();

        $options = $this->getClientRequestOptions();
        $idsString = implode(',', $ids);
        $uri = "/api/{$this->getApiVersion()}/{$this->getResourceName()}/{$idsString}/_batch";
        $response = $this->request('DELETE', $uri, $options);
        if (is_null($response)) {
            return 0;
        }
        return count($ids);
    }

    /**
     * Get Resource Paginator.
     *
     * @return \Hyperf\Paginator\LengthAwarePaginator
     */
    public function list(Query $query)
    {
        $query = $this->parseQuery($query);
        $options = $this->getClientRequestOptions();
        $path = "/api/{$this->getApiVersion()}/{$this->getResourceName()}";
        $queryString = $query->toUriQueryString($this->getVersion());
        $uri = $path . '?' . $queryString;
        $response = $this->request('GET', $uri, $options);
        if (is_null($response)) {
            return new LengthAwarePaginator(
                [],
                0,
                $query->getQueryPagination()->getPage(),
                $query->getQueryPagination()->getPerPage(),
                [
                    'path' => $this->getBaseUri() . $uri,
                ]
            );
        }
        $data = $this->decodeResponse($response);
        return new LengthAwarePaginator(
            $data['data'],
            $data['meta']['pagination']['total'] ?? 0,
            $data['meta']['pagination']['per_page'] ?? 10,
            $data['meta']['pagination']['current_page'] ?? 1,
            [
                'path' => $this->getBaseUri() . $uri,
            ]
        );
    }

    /**
     * Get Resource By Query.
     */
    public function getByQuery(Query $query): array
    {
        $query = $this->parseQuery($query);

        if ($this->isVersion('v1')) {
            return $this->getByQueryWithVersionV1($query);
        }

        $options = $this->getClientRequestOptions();
        $path = "/api/{$this->getApiVersion()}/{$this->getResourceName()}/_batch";
        $queryString = $query->toUriQueryString($this->getVersion());
        $uri = $path . '?' . $queryString;
        $response = $this->request('GET', $uri, $options);
        if (is_null($response)) {
            return [];
        }
        $data = $this->decodeResponse($response);
        return $data['data'] ?? [];
    }

    /**
     * Get the value of headerResolver.
     */
    public function getHeaderResolver()
    {
        return $this->headerResolver;
    }

    /**
     * Set the value of headerResolver.
     *
     * @param mixed $headerResolver
     * @return self
     */
    public function setHeaderResolver($headerResolver)
    {
        if (is_null($headerResolver)) {
            $headerResolver = new DefaultHeaderResolver();
        }
        $this->headerResolver = $headerResolver;

        return $this;
    }

    public function isVersion(string $version): bool
    {
        return $this->version === $version;
    }

    /**
     * Get undocumented variable.
     *
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Set undocumented variable.
     *
     * @param LoggerInterface $logger Undocumented variable
     *
     * @return self
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Get Resource aggregate By Query.
     *
     * @return array
     *
     * [
     *   'count' => [
     *          'id' => 1,
     *          'score' => 3
     *      ],
     *   'sum' => [
     *          'money' => 10,
     *          'score' => 3
     *      ],
     * ]
     */
    public function aggregate(Query $query): array
    {
        $query = $this->parseQuery($query);

        $options = $this->getClientRequestOptions();
        $path = "/api/{$this->getApiVersion()}/{$this->getResourceName()}/_aggregate";
        $queryString = $query->toUriQueryString($this->getVersion());
        $uri = $path . '?' . $queryString;
        $response = $this->request('GET', $uri, $options);
        if (is_null($response)) {
            return [];
        }
        $data = $this->decodeResponse($response);
        return $data['data'] ?? [];
    }

    /**
     * Get the value of apiVersion.
     */
    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    /**
     * Set the value of apiVersion.
     *
     * @param mixed $apiVersion
     * @return self
     */
    public function setApiVersion($apiVersion)
    {
        $this->apiVersion = $apiVersion;

        return $this;
    }

    protected function parseQuery(Query $query): Query
    {
        if ($query->hasQuerySelect()) {
            $query->setQuerySelect(new QuerySelect($this->getParsedSelect($query->getQuerySelect()->getSelects())));
        }
        return $query;
    }

    protected function getClientRequestOptions(): array
    {
        return [
            'base_uri' => $this->getBaseUri(),
            'connect_timeout' => $this->getConnectTimeout(),
            'timeout' => $this->getTimeout(),
            'headers' => $this->getCommonHeaders() + [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'api.client v1.0',
                'Authorization' => 'b9ddbadb4ebbf06110b93d98adb1497c',
            ],
            'debug' => $this->getDebug(),
        ];
    }

    protected function decodeResponse(ResponseInterface $response): array
    {
        $data = json_decode($response->getBody()->getContents(), true);
        if (is_array($data)) {
            return $data;
        }
        return [];
    }

    protected function request(string $method, string $uri, array $options): ?ResponseInterface
    {
        try {
            return $this->getClient()
                ->request($method, $uri, $options);
        } catch (\Exception $e) {
            if ($this->logger) {
                $error = "{$method} uri: {$uri} exception: {$e->getMessage()}";
                $this->logger->error($error, $e->getTrace());
            }
            return null;
            // throw $e;
        }
    }

    protected function throwExceptionIfNotSupported()
    {
        if ($this->isVersion('v1')) {
            throw new \RuntimeException('api not supported by version:' . $this->version);
        }
    }

    protected function getByQueryWithVersionV1(Query $query)
    {
        $options = $this->getClientRequestOptions();
        $path = "/api/{$this->getApiVersion()}/{$this->getResourceName()}";
        $queryString = $query->toUriQueryString($this->getVersion());
        $uri = $path . '?' . $queryString;
        $response = $this->request('GET', $uri, $options);
        $data = $this->decodeResponse($response);
        if ($query->getQueryPagination() && isset($data['data']['data'])) {
            return $data['data']['data'];
        }
        return $data['data'];
    }
}
