<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Entity;

use Hyperf\Utils\Context;
use Tusimo\Resource\Constants\Header;
use Psr\Http\Message\ServerRequestInterface;

trait RequestHeaderTrait
{
    public function getUserId(): string
    {
        return $this->header(Header::X_USER_ID, '');
    }

    public function getConsumerName(): string
    {
        return $this->header(Header::X_CONSUMER_NAME, '');
    }

    public function getApp(): string
    {
        return $this->header(Header::X_APP, '');
    }

    public function getClientPlatform(): string
    {
        return $this->header(Header::X_CLIENT_PLATFORM, '');
    }

    public function getClientVersion(): string
    {
        return $this->header(Header::X_CLIENT_VERSION, '');
    }

    public function getClientVersionCode(): string
    {
        return $this->header(Header::X_CLIENT_VERSION_CODE, '');
    }

    public function getClientPackage(): string
    {
        return $this->header(Header::X_CLIENT_PACKAGE, '');
    }

    public function getClientAppName(): string
    {
        return $this->header(Header::X_CLIENT_APP_NAME, '');
    }

    public function getClientDeviceId(): string
    {
        return $this->header(Header::X_CLIENT_DEVICE_ID, '');
    }

    public function getClientChannel(): string
    {
        return $this->header(Header::X_CLIENT_CHANNEL, '');
    }

    public function getRealIp(): string
    {
        return $this->header(Header::X_REAL_IP, '');
    }

    public function getClientRefer(): string
    {
        return $this->header(Header::X_CLIENT_REFER, '');
    }

    public function setUserId(string $userId): ResourceRequest
    {
        return $this->setHeader(Header::X_USER_ID, $userId);
    }

    public function setConsumerName(string $consumerName): ResourceRequest
    {
        return $this->setHeader(Header::X_CONSUMER_NAME, $consumerName);
    }

    public function setApp(string $app): ResourceRequest
    {
        return $this->setHeader(Header::X_APP, $app);
    }

    public function setClientPlatform(string $clientPlatform): ResourceRequest
    {
        return $this->setHeader(Header::X_CLIENT_PLATFORM, $clientPlatform);
    }

    public function setClientVersion(string $clientVersion): ResourceRequest
    {
        return $this->setHeader(Header::X_CLIENT_VERSION, $clientVersion);
    }

    public function setClientVersionCode(string $clientVersionCode): ResourceRequest
    {
        return $this->setHeader(Header::X_CLIENT_VERSION_CODE, $clientVersionCode);
    }

    public function setClientPackage(string $clientPackage): ResourceRequest
    {
        return $this->setHeader(Header::X_CLIENT_PACKAGE, $clientPackage);
    }

    public function setClientAppName(string $clientAppName): ResourceRequest
    {
        return $this->setHeader(Header::X_CLIENT_APP_NAME, $clientAppName);
    }

    public function setClientDeviceId(string $clientDeviceId): ResourceRequest
    {
        return $this->setHeader(Header::X_CLIENT_DEVICE_ID, $clientDeviceId);
    }

    public function setClientChannel(string $clientChannel): ResourceRequest
    {
        return $this->setHeader(Header::X_CLIENT_CHANNEL, $clientChannel);
    }

    public function setRealIp(string $realIp): ResourceRequest
    {
        return $this->setHeader(Header::X_REAL_IP, $realIp);
    }

    public function setClientRefer(string $clientRefer): ResourceRequest
    {
        return $this->setHeader(Header::X_CLIENT_REFER, $clientRefer);
    }

    public function getRequestId(): string
    {
        return $this->header(Header::X_REQUEST_ID, '');
    }

    public function setRequestId(string $requestId): ResourceRequest
    {
        return $this->setHeader(Header::X_REQUEST_ID, $requestId);
    }

    public function getMeta($key)
    {
        return $this->header($key, '');
    }

    public function setMeta($key, $meta): ResourceRequest
    {
        return $this->setHeader($key, $meta);
    }

    public function setHeader(string $header, string $value): ResourceRequest
    {
        $request = $this->withHeader($header, $value);
        return $this->build($request);
    }

    protected function build($request): ResourceRequest
    {
        Context::set(ServerRequestInterface::class, $request);
        return new ResourceRequest();
    }
}
