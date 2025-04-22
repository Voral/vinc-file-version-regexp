<?php

namespace Vasoft\VersionIncrement\Extension\Exception;

use Throwable;
use Vasoft\VersionIncrement\Exceptions\UserException;

class FileNotWritableException extends UserException
{
    private const CODE = 2;

    public function __construct(string $fileName, ?Throwable $previous = null)
    {
        parent::__construct(
            self::CODE,
            "File not writable: {$fileName}",
            $previous
        );
    }
}