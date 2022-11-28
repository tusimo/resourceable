<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Model;

use Hyperf\Utils\ApplicationContext;
use Hyperf\Database\ConnectionInterface;
use Hyperf\Database\ConnectionResolverInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class Register
{
    /**
     * The connection resolver instance.
     *
     * @var ConnectionResolverInterface
     */
    protected static $resolver;

    /**
     * @var EventDispatcherInterface
     */
    protected static $dispatcher;

    /**
     * Undocumented variable.
     *
     * @var ValidatorFactoryInterface
     */
    protected static $validatorFactory;

    /**
     * Resolve a connection instance.
     *
     * @param null|string $connection
     * @return ConnectionInterface
     */
    public static function resolveConnection($connection = null)
    {
        return static::$resolver->connection($connection);
    }

    /**
     * Get the connection resolver instance.
     *
     * @return ConnectionResolverInterface
     */
    public static function getConnectionResolver()
    {
        return static::$resolver;
    }

    /**
     * Set the connection resolver instance.
     */
    public static function setConnectionResolver(ConnectionResolverInterface $resolver)
    {
        static::$resolver = $resolver;
    }

    /**
     * Unset the connection resolver for models.
     */
    public static function unsetConnectionResolver()
    {
        static::$resolver = null;
    }

    /**
     * Get the event dispatcher instance.
     */
    public static function getEventDispatcher(): ?EventDispatcherInterface
    {
        if (ApplicationContext::hasContainer()) {
            return ApplicationContext::getContainer()->get(EventDispatcherInterface::class);
        }
        return static::$dispatcher;
    }

    /**
     * Set the event dispatcher instance.
     */
    public static function setEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        static::$dispatcher = $dispatcher;
    }

    /**
     * Unset the event dispatcher for models.
     */
    public static function unsetEventDispatcher()
    {
        static::$dispatcher = null;
    }

    /**
     * Get undocumented variable.
     *
     * @return ValidatorFactoryInterface
     */
    public static function getValidatorFactory(): ?ValidatorFactoryInterface
    {
        if (ApplicationContext::hasContainer()) {
            return ApplicationContext::getContainer()->get(ValidatorFactoryInterface::class);
        }
        return static::$validatorFactory;
    }

    /**
     * Set undocumented variable.
     *
     * @param ValidatorFactoryInterface $validatorFactory Undocumented variable
     */
    public static function setValidatorFactory(ValidatorFactoryInterface $validatorFactory)
    {
        static::$validatorFactory = $validatorFactory;
    }

    /**
     * Unset ValidatorFactory.
     */
    public static function unSetValidatorFactory()
    {
        static::$validatorFactory = null;
    }
}
