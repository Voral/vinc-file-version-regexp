<?php

declare(strict_types=1);

namespace Vasoft\VersionIncrement\Extension;

use PHPUnit\Framework\TestCase;
use Vasoft;
use Vasoft\VersionIncrement\Events\EventType;

include_once __DIR__ . '/MockTrait.php';

/**
 * @internal
 *
 * @coversDefaultClass \Vasoft\VersionIncrement\Extension\RegexpFileModifier
 */
final class RegexpFileModifierTest extends TestCase
{
    use MockTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->initMocks();
    }

    public function testNormalWork(): void
    {
        $contentBefore = <<<'PHP'
            <?php
                $version="v1.0.0";
            PHP;
        $contentAfter = <<<'PHP'
            <?php
                $version="v2.0.0";
            PHP;

        $this->clearFileGetContents($contentBefore);
        $this->clearFilePutContents();
        $this->clearIsWritable(true);
        $this->clearFileExists(true);

        $modifier = new RegexpFileModifier(
            './tests/version.php',
            '#(\$version\s*=\s*"[^"]*)\d+\.\d+\.\d+([^"]*";)#s',
        );
        $event = new Vasoft\VersionIncrement\Events\Event(EventType::AFTER_VERSION_SET, '2.0.0');

        $modifier->handle($event);

        self::assertSame(1, self::$mockFileGetContentsCount);
        self::assertSame(1, self::$mockFilePutContentsCount);
        self::assertSame(1, self::$mockIsWritableCount);
        self::assertSame('./tests/version.php', self::$mockFileGetContentsParam);
        self::assertSame($contentBefore, self::$mockFileGetContentsResult);
        self::assertSame('./tests/version.php', self::$mockFilePutContentsParamPath);
        self::assertSame($contentAfter, self::$mockFilePutContentsParamContent);
    }

    /**
     * @throws Vasoft\VersionIncrement\Exceptions\ApplicationException
     *
     * @dataProvider provideTemplateNotFoundCases
     */
    public function testNotExists(int $delta): void
    {
        $contentBefore = <<<'PHP'
            <?php
                $version="v1.0.0";
            PHP;

        $this->clearFileGetContents($contentBefore);
        $this->clearFilePutContents();
        $this->clearIsWritable(true);
        $this->clearFileExists(false);

        $modifier = new RegexpFileModifier(
            './tests/version.php',
            '#(\$version\s*=\s*"[^"]*)\d+\.\d+\.\d+([^"]*";)#s',
            $delta,
        );
        $event = new Vasoft\VersionIncrement\Events\Event(EventType::AFTER_VERSION_SET, '2.0.0');
        self::expectException(Exception\FileNotFoundException::class);
        self::expectExceptionMessage('File not found: ./tests/version.php');
        self::expectExceptionCode(5001 + $delta);
        $modifier->handle($event);
    }

    /**
     * @throws Vasoft\VersionIncrement\Exceptions\ApplicationException
     *
     * @dataProvider provideTemplateNotFoundCases
     */
    public function testNotWritable(int $delta): void
    {
        $contentBefore = <<<'PHP'
            <?php
                $version="v1.0.0";
            PHP;

        $this->clearFileGetContents($contentBefore);
        $this->clearFilePutContents();
        $this->clearIsWritable(false);
        $this->clearFileExists(true);

        $modifier = new RegexpFileModifier(
            './tests/version2.php',
            '#(\$version\s*=\s*"[^"]*)\d+\.\d+\.\d+([^"]*";)#s',
            $delta,
        );
        $event = new Vasoft\VersionIncrement\Events\Event(EventType::AFTER_VERSION_SET, '2.0.0');
        self::expectException(Exception\FileNotWritableException::class);
        self::expectExceptionMessage('File not writable: ./tests/version2.php');
        self::expectExceptionCode(5002 + $delta);
        $modifier->handle($event);
    }

    /**
     * @throws Vasoft\VersionIncrement\Exceptions\ApplicationException
     *
     * @dataProvider provideTemplateNotFoundCases
     */
    public function testTemplateNotFound(int $delta): void
    {
        $contentBefore = <<<'PHP'
            <?php
                $versionMain="1.0.0";
            PHP;

        $this->clearFileGetContents($contentBefore);
        $this->clearFilePutContents();
        $this->clearIsWritable(true);
        $this->clearFileExists(true);

        $modifier = new RegexpFileModifier(
            './tests/version2.php',
            '#(\$version\s*=\s*"[^"]*)\d+\.\d+\.\d+([^"]*";)#s',
            $delta,
        );
        $event = new Vasoft\VersionIncrement\Events\Event(EventType::AFTER_VERSION_SET, '2.0.0');
        self::expectException(Exception\PatternNotFoundException::class);
        self::expectExceptionMessage('The specified pattern was not found in the file: ./tests/version2.php');
        self::expectExceptionCode(5003 + $delta);
        $modifier->handle($event);
    }

    public static function provideTemplateNotFoundCases(): iterable
    {
        return [[0], [10]];
    }
}
