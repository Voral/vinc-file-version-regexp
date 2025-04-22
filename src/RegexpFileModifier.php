<?php

declare(strict_types=1);

namespace Vasoft\VersionIncrement\Extension;

use Vasoft\VersionIncrement\Contract\EventListenerInterface;
use Vasoft\VersionIncrement\Events\Event;
use Vasoft\VersionIncrement\Extension\Exception\FileNotFoundException;
use Vasoft\VersionIncrement\Extension\Exception\FileNotWritableException;
use Vasoft\VersionIncrement\Extension\Exception\PatternNotFoundException;

class RegexpFileModifier implements EventListenerInterface
{
    public function __construct(
        private readonly string $filePath,
        private readonly string $regexp,
    ) {}

    public function handle(Event $event): void
    {
        if (!empty($event->version)) {
            $this->checkFile();
            $this->replace($event);
        }
    }

    /**
     * @throws FileNotFoundException
     * @throws FileNotWritableException
     */
    private function checkFile(): void
    {
        if (!file_exists($this->filePath)) {
            throw new FileNotFoundException($this->filePath);
        }
        if (!is_writable($this->filePath)) {
            throw new FileNotWritableException($this->filePath);
        }
    }

    /**
     * @throws PatternNotFoundException
     */
    private function replace(Event $event): void
    {
        $newVersion = $event->version;
        $content = file_get_contents($this->filePath);

        $updatedContent = preg_replace_callback(
            $this->regexp,
            static fn($matches) => $matches[1] . $newVersion . $matches[2],
            $content,
        );
        if (null === $updatedContent || $content === $updatedContent) {
            throw new PatternNotFoundException($this->filePath);
        }

        file_put_contents($this->filePath, $updatedContent);
    }
}
