<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Entity;

use Hyperf\Utils\Arr;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Paginator\LengthAwarePaginator;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

class ResourceResponse
{
    private array $content = [
        'code' => 200,
        'msg' => 'success',
        'data' => null,
        'meta' => null,
    ];

    private array $headers = [];

    private int  $status = 200;

    public function withContent($content): ResourceResponse
    {
        $this->content = $content;
        return $this;
    }

    public function withMeta(string $metaName, $metaData): ResourceResponse
    {
        $this->content['meta'][$metaName] = $metaData;
        return $this;
    }

    public function withCode(int $code): ResourceResponse
    {
        $this->content['code'] = $code;
        return $this;
    }

    public function withMsg(string $msg): ResourceResponse
    {
        $this->content['msg'] = $msg;
        return $this;
    }

    public function withData($data): ResourceResponse
    {
        $this->content['data'] = $data;
        return $this;
    }

    public function paginator(LengthAwarePaginator $paginator): ResourceResponse
    {
        $array = $paginator->toArray();
        return $this->withData($paginator->items())
            ->withMeta('paginator', Arr::except($array, 'data'))
            ->withMsg('success')->withCode(200)
            ->withStatus(200)->send();
    }

    public function success($data, string $msg = 'success', int $code = 200): ResourceResponse
    {
        return $this->withData($data)->withMsg($msg)
            ->withCode($code)->withStatus(200)->send();
    }

    public function error(string $msg, int $code = 400): ResourceResponse
    {
        return $this->withMsg($msg)->withCode($code)
            ->withStatus(200)->withData(null)->send();
    }

    public function noContent(): ResourceResponse
    {
        return $this->withContent([])->withStatus(204)->send();
    }

    public function notFound(): ResourceResponse
    {
        return $this->withContent([])->withStatus(404)->send();
    }

    public function accepted($data = []): ResourceResponse
    {
        return $this->withData($data)
            ->withMsg('resource accepted')->withCode(202)->withStatus(202)->send();
    }

    public function created($data = []): ResourceResponse
    {
        return $this->withData($data)
            ->withMsg('resource created')->withCode(201)->withStatus(201)->send();
    }

    public function withStatus(int $status): ResourceResponse
    {
        $this->status = $status;
        return $this;
    }

    public function withHeader(string $key, $value): ResourceResponse
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * Get the value of content.
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Get the value of headers.
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get the value of status.
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function attachToResponse(ResponseInterface $response): PsrResponseInterface
    {
        if ($this->getContent()) {
            $response = $response->json($this->getContent());
        } else {
            $response = $response->withBody(new SwooleStream());
        }
        $response = $response->withStatus($this->getStatus());
        foreach ($this->getHeaders() as $key => $header) {
            $response = $response->withHeader($key, $header);
        }
        return $response;
    }

    public function toResponse(): PsrResponseInterface
    {
        return $this->attachToResponse(container()->get(ResponseInterface::class));
    }

    private function send(): ResourceResponse
    {
        $appName = container()->get(ConfigInterface::class)->get('app_name', 'X-Engine');
        $this->withHeader('Server', $appName);
        return $this;
    }
}
