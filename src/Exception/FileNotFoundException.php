<?php

declare(strict_types=1);

namespace Vasoft\VersionIncrement\Extension\Exception;

use Vasoft\VersionIncrement\Exceptions\UserException;

class FileNotFoundException extends UserException
{
    private const CODE = 1;

    public function __construct(string $fileName, ?\Throwable $previous = null)
    {
        parent::__construct(self::CODE, "File not found: {$fileName}", $previous);
    }
}
