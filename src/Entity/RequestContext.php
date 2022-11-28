<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Entity;

use Hyperf\Utils\Fluent;
use Hyperf\Context\Context;
use Tusimo\Resource\Constants\Header;
use Hyperf\Contract\CompressInterface;
use Hyperf\Contract\UnCompressInterface;

class RequestContext extends Fluent implements CompressInterface, UnCompressInterface
{
    /**
     * Get the value of userId.
     */
    public function getUserId()
    {
        return $this->getData(Header::X_USER_ID);
    }

    /**
     * Set the value of userId.
     *
     * @param mixed $userId
     * @return self
     */
    public function setUserId($userId)
    {
        return $this->setData(Header::X_USER_ID, $userId);
    }

    /**
     * Get the value of app.
     */
    public function getApp()
    {
        return $this->getData(Header::X_APP);
    }

    /**
     * Set the value of app.
     *
     * @param mixed $app
     * @return self
     */
    public function setApp($app)
    {
        return $this->setData(Header::X_APP, $app);
    }

    /**
     * Get the value of clientPlatform.
     */
    public function getClientPlatform()
    {
        return $this->getData(Header::X_CLIENT_PLATFORM);
    }

    /**
     * Set the value of clientPlatform.
     *
     * @param mixed $clientPlatform
     * @return self
     */
    public function setClientPlatform($clientPlatform)
    {
        return $this->setData(Header::X_CLIENT_PLATFORM, $clientPlatform);
    }

    /**
     * Get the value of clientVersion.
     */
    public function getClientVersion()
    {
        return $this->getData(Header::X_CLIENT_VERSION);
    }

    /**
     * Set the value of clientVersion.
     *
     * @param mixed $clientVersion
     * @return self
     */
    public function setClientVersion($clientVersion)
    {
        return $this->setData(Header::X_CLIENT_VERSION, $clientVersion);
    }

    /**
     * Get the value of clientVersionCode.
     */
    public function getClientVersionCode()
    {
        return $this->getData(Header::X_CLIENT_VERSION_CODE);
    }

    /**
     * Set the value of clientVersionCode.
     *
     * @param mixed $clientVersionCode
     * @return self
     */
    public function setClientVersionCode($clientVersionCode)
    {
        return $this->setData(Header::X_CLIENT_VERSION_CODE, $clientVersionCode);
    }

    /**
     * Get the value of clientAppName.
     */
    public function getClientAppName()
    {
        return $this->getData(Header::X_CLIENT_APP_NAME);
    }

    /**
     * Set the value of clientAppName.
     *
     * @param mixed $clientAppName
     * @return self
     */
    public function setClientAppName($clientAppName)
    {
        return $this->setData(Header::X_CLIENT_APP_NAME, $clientAppName);
    }

    /**
     * Get the value of clientDeviceId.
     */
    public function getClientDeviceId()
    {
        return $this->getData(Header::X_CLIENT_DEVICE_ID);
    }

    /**
     * Set the value of clientDeviceId.
     *
     * @param mixed $clientDeviceId
     * @return self
     */
    public function setClientDeviceId($clientDeviceId)
    {
        return $this->setData(Header::X_CLIENT_DEVICE_ID, $clientDeviceId);
    }

    /**
     * Get the value of clientChannel.
     */
    public function getClientChannel()
    {
        return $this->getData(Header::X_CLIENT_CHANNEL);
    }

    /**
     * Set the value of clientChannel.
     *
     * @param mixed $clientChannel
     * @return self
     */
    public function setClientChannel($clientChannel)
    {
        return $this->setData(Header::X_CLIENT_CHANNEL, $clientChannel);
    }

    /**
     * Get the value of realIp.
     */
    public function getRealIp()
    {
        return $this->getData(Header::X_REAL_IP);
    }

    /**
     * Set the value of realIp.
     *
     * @param mixed $realIp
     * @return self
     */
    public function setRealIp($realIp)
    {
        return $this->setData(Header::X_REAL_IP, $realIp);
    }

    /**
     * Get the value of clientRefer.
     */
    public function getClientRefer()
    {
        return $this->getData(Header::X_CLIENT_REFER);
    }

    /**
     * Set the value of clientRefer.
     *
     * @param mixed $clientRefer
     * @return self
     */
    public function setClientRefer($clientRefer)
    {
        return $this->setData(Header::X_CLIENT_REFER, $clientRefer);
    }

    /**
     * Get the value of requestId.
     */
    public function getRequestId()
    {
        return $this->getData(Header::X_REQUEST_ID);
    }

    /**
     * Set the value of requestId.
     *
     * @param mixed $requestId
     * @return self
     */
    public function setRequestId($requestId)
    {
        return $this->setData(Header::X_REQUEST_ID, $requestId);
    }

    /**
     * Get Language.
     */
    public function getLanguage()
    {
        return $this->getData(Header::X_LANGUAGE);
    }

    /**
     * Set Language.
     *
     * @param string $language
     * @return self
     */
    public function setLanguage($language)
    {
        return $this->setData(HEADER::X_LANGUAGE, $language);
    }

    /**
     * @return null|mixed
     */
    public function getClientPackage()
    {
        return $this->getData(Header::X_CLIENT_PACKAGE);
    }

    /**
     * @return $this
     */
    public function setClientPackage($clientPackage)
    {
        return $this->setData(Header::X_CLIENT_PACKAGE, $clientPackage);
    }

    /**
     * @return null|mixed
     */
    public function getConsumer()
    {
        return $this->getData(Header::X_CONSUMER_NAME);
    }

    /**
     * @return $this
     */
    public function setConsumer($consumerName)
    {
        return $this->setData(Header::X_CONSUMER_NAME, $consumerName);
    }

    /**
     * Get the value of meta.
     * @param mixed $key
     */
    public function getMeta($key)
    {
        return $this->getData($key);
    }

    /**
     * Set the value of meta.
     *
     * @param mixed $meta
     * @param mixed $key
     * @param mixed $data
     * @return self
     */
    public function setMeta($key, $data)
    {
        return $this->setData($key, $data);
    }

    public function getData($key, $default = '')
    {
        return $this->{$key} ?? $default;
    }

    public function setData(string $key, $data): self
    {
        $instance = clone $this;
        $instance->{$key} = $data;
        static::setRequestContext($instance);
        return $instance;
    }

    public static function createFromRequest(ResourceRequest $request): self
    {
        $data = [];
        foreach (Header::getAllHeaderKeys() as $key) {
            $data[$key] = $request->header($key);
        }
        return static::createFromArray($data);
    }

    public static function createFromArray(array $data): self
    {
        $context = new self($data);
        static::setRequestContext($context);
        return $context;
    }

    public static function getRequestContext(): ?RequestContext
    {
        return Context::get('request_context');
    }

    public static function setRequestContext(RequestContext $requestContext)
    {
        Context::set('request_context', $requestContext);
    }

    public function compress(): UnCompressInterface
    {
        return $this;
    }

    public function uncompress()
    {
        return static::createFromArray($this->toArray());
    }

    public function toHeaders(): array
    {
        return $this->toArray();
    }
}
