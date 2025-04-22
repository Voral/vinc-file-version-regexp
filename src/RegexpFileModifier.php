<?php

declare(strict_types=1);

namespace Vasoft\VersionIncrement\Extension;

use Vasoft\VersionIncrement\Contract\EventListenerInterface;
use Vasoft\VersionIncrement\Events\Event;
use Vasoft\VersionIncrement\Extension\Exception\FileNotFoundException;
use Vasoft\VersionIncrement\Extension\Exception\FileNotWritableException;
use Vasoft\VersionIncrement\Extension\Exception\PatternNotFoundException;

/**
 * Class RegexpFileModifier.
 *
 * This class is an extension for the `voral/version-increment` package, designed to update version strings in arbitrary files
 * using regular expressions. It listens to the `BEFORE_VERSION_SET` event and performs version replacements in the specified file
 * based on the provided regular expression.
 */
class RegexpFileModifier implements EventListenerInterface
{
    /**
     * Constructor for the RegexpFileModifier class.
     *
     * Initializes the file path, regular expression, and optional error code delta.
     *
     * @param string $filePath       the path to the file where the version will be updated
     * @param string $regexp         The regular expression used to locate the version string in the file.
     *                               It must contain capturing groups:
     *                               - Group 1: The part of the string before the version.
     *                               - Group 2: The part of the string after the version.
     * @param int    $errorCodeDelta Optional delta value to ensure unique error codes across multiple modules.
     *                               Defaults to `0`.
     */
    public function __construct(
        private readonly string $filePath,
        private readonly string $regexp,
        private readonly int $errorCodeDelta = 0,
    ) {}

    /**
     * Handles the event and updates the version in the specified file.
     *
     * This method is called when an event is dispatched. If the event contains a new version,
     * it checks the file's existence and writability, then replaces the version in the file
     * using the provided regular expression.
     *
     * @param Event $event the event object containing the new version in the `version` property
     *
     * @throws FileNotFoundException    if the specified file does not exist
     * @throws FileNotWritableException if the specified file is not writable
     * @throws PatternNotFoundException if the regular expression does not match any part of the file content
     */
    public function handle(Event $event): void
    {
        if (!empty($event->version)) {
            $this->checkFile();
            $this->replace($event);
        }
    }

    /**
     * Checks if the file exists and is writable.
     *
     * This method ensures that the file specified in the constructor exists and can be modified.
     * If the file does not exist or is not writable, an exception is thrown.
     *
     * @throws FileNotFoundException    if the file does not exist
     * @throws FileNotWritableException if the file is not writable
     */
    private function checkFile(): void
    {
        if (!file_exists($this->filePath)) {
            throw new FileNotFoundException($this->filePath, $this->errorCodeDelta);
        }
        if (!is_writable($this->filePath)) {
            throw new FileNotWritableException($this->filePath, $this->errorCodeDelta);
        }
    }

    /**
     * Replaces the version in the file using the provided regular expression.
     *
     * This method reads the file content, applies the regular expression to locate the version,
     * and replaces it with the new version from the event. If the regular expression does not
     * find a match, an exception is thrown.
     *
     * @param Event $event the event object containing the new version in the `version` property
     *
     * @throws PatternNotFoundException if the regular expression does not match any part of the file content
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
            throw new PatternNotFoundException($this->filePath, $this->errorCodeDelta);
        }

        file_put_contents($this->filePath, $updatedContent);
    }
}
