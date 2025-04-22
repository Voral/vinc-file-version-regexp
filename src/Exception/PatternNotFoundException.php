<?php

namespace Vasoft\VersionIncrement\Extension\Exception;

use Throwable;
use Vasoft\VersionIncrement\Exceptions\UserException;

class PatternNotFoundException extends UserException
{
    private const CODE = 3;

    public function __construct(string $fileName, ?Throwable $previous = null)
    {
        parent::__construct(
            self::CODE,
            "The specified pattern was not found in the file: {$fileName}",
            $previous
        );
    }
}