<?php

declare(strict_types=1);

namespace Vasoft\VersionIncrement\Extension;

use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;

trait MockTrait
{
    use PHPMock;

    private bool $initialized = false;

    protected static int $mockFileGetContentsCount = 0;
    protected static string $mockFileGetContentsResult = '';
    protected static string $mockFileGetContentsParam = '';
    protected static int $mockIsWritableCount = 0;
    protected static bool $mockIsWritableResult = false;
    protected static string $mockIsWritableParam = '';
    protected static int $mockFileExistsCount = 0;
    protected static bool $mockFileExistsResult = false;
    protected static string $mockFileExistsParam = '';
    protected static int $mockFilePutContentsCount = 0;
    protected static string $mockFilePutContentsParamPath = '';
    protected static string $mockFilePutContentsParamContent = '';

    protected function initMocks(): void
    {
        if (!$this->initialized) {
            $mockFileGetContents = $this->getFunctionMock(__NAMESPACE__, 'file_get_contents');
            $mockFileGetContents->expects(TestCase::any())->willReturnCallback(
                static function (string $path): false|string {
                    self::$mockFileGetContentsParam = $path;
                    ++self::$mockFileGetContentsCount;

                    return self::$mockFileGetContentsResult;
                },
            );
            $mockFilePutContents = $this->getFunctionMock(__NAMESPACE__, 'file_put_contents');
            $mockFilePutContents->expects(TestCase::any())->willReturnCallback(
                static function (string $path, string $content): false|int {
                    self::$mockFilePutContentsParamPath = $path;
                    self::$mockFilePutContentsParamContent = $content;
                    ++self::$mockFilePutContentsCount;

                    return strlen($content);
                },
            );
            $mockIsWritable = $this->getFunctionMock(__NAMESPACE__, 'is_writable');
            $mockIsWritable->expects(TestCase::any())->willReturnCallback(
                static function (string $path): bool {
                    self::$mockIsWritableParam = $path;
                    ++self::$mockIsWritableCount;

                    return self::$mockIsWritableResult;
                },
            );
            $mockFileExists = $this->getFunctionMock(__NAMESPACE__, 'file_exists');
            $mockFileExists->expects(TestCase::any())->willReturnCallback(
                static function (string $path): bool {
                    self::$mockFileExistsParam = $path;
                    ++self::$mockFileExistsCount;

                    return self::$mockFileExistsResult;
                },
            );
            $this->initialized = true;
        }
    }

    protected function clearFileGetContents(string $result): void
    {
        self::$mockFileGetContentsCount = 0;
        self::$mockFileGetContentsResult = $result;
        self::$mockFileGetContentsParam = '';
    }

    protected function clearIsWritable(bool $result): void
    {
        self::$mockIsWritableCount = 0;
        self::$mockIsWritableResult = $result;
        self::$mockIsWritableParam = '';
    }

    protected function clearFileExists(bool $result): void
    {
        self::$mockFileExistsCount = 0;
        self::$mockFileExistsResult = $result;
        self::$mockFileExistsParam = '';
    }

    protected function clearFilePutContents(): void
    {
        self::$mockFilePutContentsCount = 0;
        self::$mockFilePutContentsParamPath = '';
        self::$mockFilePutContentsParamContent = '';
    }
}
