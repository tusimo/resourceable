<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Exception\Service;

class ServiceException extends \RuntimeException
{
    protected $statusCode = 400;

    public function __construct(
        string $message = '',
        int $code = 400,
        ?\Throwable $previous = null,
        int $statusCode = 400
    ) {
        parent::__construct($message, $code, $previous);
        $this->statusCode = $statusCode;
    }
}
