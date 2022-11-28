<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Component;

use Psr\Log\LoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Config\Annotation\Value;
use Tusimo\Resource\Concerns\HasClientResolvable;
use Tusimo\Resource\Contract\ClientResolverContract;

class YarHttpClient
{
    use HasClientResolvable;

    /**
     * Logger.
     * @Inject
     */
    protected LoggerInterface $logger;

    /**
     * Servers.
     *
     * @Value("servers")
     */
    protected array $servers;

    /**
     * Server auth key.
     *
     * @Value("app.auth")
     */
    protected $auth;

    /**
     * Target Server.
     */
    protected string $server;

    /**
     * Target Model.
     */
    protected string $model;

    public function __construct(
        string $server,
        string $model,
        ClientResolverContract $clientResolver = null
    ) {
        $this->server = $server;
        $this->model = $model;
        $this->setClientResolver($clientResolver);
    }

    public function __call($method, $args)
    {
        return $this->call($method, $args);
    }

    public function call(string $function, array $parameters)
    {
        $options = [
            'base_uri' => $this->getBaseUri(),
            'connect_timeout' => $this->getConnectTimeout(),
            'timeout' => $this->getTimeout(),
            'headers' => $this->getHeaders(),
            'query' => $this->getQuery(),
            'json' => [
                'model' => $this->model,
                'function' => $function,
                'parameters' => $parameters,
            ],
            'debug' => is_local() ? true : false,
        ];
        $result = null;
        try {
            $response = $this->getClient()->request(
                'POST',
                '/api/v0',
                $options
            );
            $content = $response->getBody()->getContents();
            $this->logger->info('yar http get response:' . $content);
            $result = json_decode($content, true);
        } catch (\Exception $e) {
            $this->logger->error('yar http request error: ' . $e->getMessage());
            $result = null;
        }
        return $this->handleResponse($result);
    }

    protected function handleResponse($result)
    {
        if (! is_array($result)) {
            return null;
        }
        if ($result['code'] !== 1) {
            $this->logger->warning('response code is :' . $result['code'], $result);
            return null;
        }
        return $result['data'] ?? null;
    }

    protected function getBaseUri(): string
    {
        return $this->servers[$this->server];
    }

    protected function getQuery(): array
    {
        return [
            'auth' => $this->auth,
            'uid' => request_context()->getUserId(),
            'caller' => config('app.name', ''),
            'ip' => request_context()->getRealIp(),
            'platform' => request_context()->getClientPlatform(),
            'client_version' => request_context()->getClientVersion(),
            'deviceid' => request_context()->getClientDeviceId(),
            'lang' => request_context()->getLanguage(),
        ];
    }

    protected function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'User-Agent' => 'melo.api.client v1.0',
            'Authorization' => 'e12f91a863eae313ae6ed20859f61720',
        ] + request_context()->toHeaders();
    }
}
