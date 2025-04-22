<?php

declare(strict_types=1);

namespace Vasoft\VersionIncrement\Extension\Exception;

use Vasoft\VersionIncrement\Exceptions\UserException;

class PatternNotFoundException extends UserException
{
    private const CODE = 3;

    public function __construct(string $fileName, int $errorCodeDelta, ?\Throwable $previous = null)
    {
        parent::__construct(
            self::CODE + $errorCodeDelta,
            "The specified pattern was not found in the file: {$fileName}",
            $previous,
        );
    }
}
