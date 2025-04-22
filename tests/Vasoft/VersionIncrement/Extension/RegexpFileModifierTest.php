<?php


namespace Vasoft\VersionIncrement\Extension;

use PHPUnit\Event\Runtime\PHP;
use PHPUnit\Framework\TestCase;
use Vasoft;
use Vasoft\VersionIncrement\Events\EventType;
use Vasoft\VersionIncrement\Extension\Exception;

include_once __DIR__ . '/MockTrait.php';
class RegexpFileModifierTest extends TestCase
{
    use Vasoft\VersionIncrement\Extension\MockTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->initMocks();
    }

    public function testNormalWork(): void
    {
        $contentBefore = <<<PHP
<?php
    \$version="v1.0.0";
PHP;
        $contentAfter = <<<PHP
<?php
    \$version="v2.0.0";
PHP;

        $this->clearFileGetContents($contentBefore);
        $this->clearFilePutContents();
        $this->clearIsWritable(true);
        $this->clearFileExists(true);

        $modifier = new RegexpFileModifier(
            './tests/version.php',
            '#(\$version\s*=\s*"[^"]*)\d+\.\d+\.\d+([^"]*";)#s'
        );
        $event = new Vasoft\VersionIncrement\Events\Event(EventType::AFTER_VERSION_SET, '2.0.0');

        $modifier->handle($event);

        $this->assertEquals(1, self::$mockFileGetContentsCount);
        $this->assertEquals(1, self::$mockFilePutContentsCount);
        $this->assertEquals(1, self::$mockIsWritableCount);
        $this->assertEquals('./tests/version.php', self::$mockFileGetContentsParam);
        $this->assertEquals($contentBefore, self::$mockFileGetContentsResult);
        $this->assertEquals('./tests/version.php', self::$mockFilePutContentsParamPath);
        $this->assertEquals($contentAfter, self::$mockFilePutContentsParamContent);
    }

    public function testNotExists(): void
    {
        $contentBefore = <<<PHP
<?php
    \$version="v1.0.0";
PHP;

        $this->clearFileGetContents($contentBefore);
        $this->clearFilePutContents();
        $this->clearIsWritable(true);
        $this->clearFileExists(false);

        $modifier = new RegexpFileModifier(
            './tests/version.php',
            '#(\$version\s*=\s*"[^"]*)\d+\.\d+\.\d+([^"]*";)#s'
        );
        $event = new Vasoft\VersionIncrement\Events\Event(EventType::AFTER_VERSION_SET, '2.0.0');
        self::expectException(Vasoft\VersionIncrement\Extension\Exception\FileNotFoundException::class);
        self::expectExceptionMessage('File not found: ./tests/version.php');
        $modifier->handle($event);
    }
    public function testNotWritable(): void
    {
        $contentBefore = <<<PHP
<?php
    \$version="v1.0.0";
PHP;

        $this->clearFileGetContents($contentBefore);
        $this->clearFilePutContents();
        $this->clearIsWritable(false);
        $this->clearFileExists(true);

        $modifier = new RegexpFileModifier(
            './tests/version2.php',
            '#(\$version\s*=\s*"[^"]*)\d+\.\d+\.\d+([^"]*";)#s'
        );
        $event = new Vasoft\VersionIncrement\Events\Event(EventType::AFTER_VERSION_SET, '2.0.0');
        self::expectException(Exception\FileNotWritableException::class);
        self::expectExceptionMessage('File not writable: ./tests/version2.php');
        $modifier->handle($event);
    }
    public function testTemplateNotFound(): void
    {
        $contentBefore = <<<PHP
<?php
    \$versionMain="1.0.0";
PHP;

        $this->clearFileGetContents($contentBefore);
        $this->clearFilePutContents();
        $this->clearIsWritable(true);
        $this->clearFileExists(true);

        $modifier = new RegexpFileModifier(
            './tests/version2.php',
            '#(\$version\s*=\s*"[^"]*)\d+\.\d+\.\d+([^"]*";)#s'
        );
        $event = new Vasoft\VersionIncrement\Events\Event(EventType::AFTER_VERSION_SET, '2.0.0');
        self::expectException(Vasoft\VersionIncrement\Extension\Exception\PatternNotFoundException::class);
        self::expectExceptionMessage('The specified pattern was not found in the file: ./tests/version2.php');
        $modifier->handle($event);
    }

}
