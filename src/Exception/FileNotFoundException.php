<?php

namespace Vasoft\VersionIncrement\Extension\Exception;

use Throwable;
use Vasoft\VersionIncrement\Exceptions\UserException;

class FileNotFoundException extends UserException
{
    private const CODE = 1;

    public function __construct(string $fileName, ?Throwable $previous = null)
    {
        parent::__construct(
            self::CODE,
            "File not found: {$fileName}",
            $previous
        );
    }
}