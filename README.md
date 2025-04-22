# vinc-file-version-regexp

[RU](README.ru.md)

![PHP Tests](https://github.com/Voral/vinc-file-version-regexp/actions/workflows/php.yml/badge.svg)
[![Code Coverage](https://scrutinizer-ci.com/g/Voral/vinc-file-version-regexp/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Voral/vinc-file-version-regexp/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Voral/vinc-file-version-regexp/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Voral/vinc-file-version-regexp/?branch=master)

An extension for [voral/version-increment](https://github.com/Voral/vs-version-incrementor) to update version strings in
custom files using regular expressions.

This tool allows you to automatically update version numbers in arbitrary files (e.g., PHP, JSON, XML) during the
versioning process. It integrates seamlessly with the `voral/version-increment` package and listens to
the `BEFORE_VERSION_SET` event to perform replacements based on a provided regular expression.

---

## Key Features

- **Custom File Support**: Update version strings in any file format using flexible regular expressions.
- **Event-Based Integration**: Listens to the `BEFORE_VERSION_SET` event from `voral/version-increment`.
- **Error Handling with Unique Codes**: Prevents conflicts between multiple extensions by supporting error code deltas.
- **Flexible Configuration**: Define custom regular expressions to match version strings in various formats.
- **Extensibility**: Easily integrate into existing workflows with minimal configuration.

---

## Installation

Install the package via Composer:

```bash
composer require --dev voral/vinc-file-version-regexp
```

---

## Usage

To use this extension, configure it in your `.vs-version-increment.php` file by adding a listener for
the `BEFORE_VERSION_SET` event. Here's an example:

```php
use Vasoft\VersionIncrement\Config;
use Vasoft\VersionIncrement\Events\EventType;
use Vasoft\VersionIncrement\Extension\RegexpFileModifier;

$config = (new Config());
$config->getEventBus()->addListener(
        EventType::BEFORE_VERSION_SET,
        new RegexpFileModifier(
            './src/version.php',
            '#(\$version\s*=\s*\'|"v)\d+\.\d+\.\d+(\'|";)#s'
        )
    );


return $config;
```

### Explanation:

- **File Path**: Specify the path to the file where the version string should be updated (e.g., `./src/version.php`).
- **Regular Expression**: Provide a regex pattern to locate the version string. The pattern must include:
    - Group 1: The part of the string before the version.
    - Group 2: The part of the string after the version.
- **Error Code Delta**: Optionally, specify a delta value to ensure unique error codes when using multiple extensions.

---

## Example Use Cases

### 1. Updating a PHP File

If your project contains a `version.php` file like this:

```php
<?php $version = "Version 1.0.0"; ?>
```

The extension will update it to:

```php
<?php $version = "Version 2.0.0"; ?>
```

### 2. Updating a JSON File

For a `config.json` file:

```json
{
  "version": "1.0.0"
}
```

The extension can update it to:

```json
{
  "version": "2.0.0"
}
```

---

## Error Handling

The extension provides detailed error messages and unique error codes to help diagnose issues:

- **FileNotFoundException**: Thrown if the specified file does not exist.
- **FileNotWritableException**: Thrown if the file is not writable.
- **PatternNotFoundException**: Thrown if the regular expression does not match any part of the file content.

Error codes are offset by a configurable `errorCodeDelta` to avoid conflicts with other extensions.

---

## Configuration Options

You can customize the behavior of the extension by adjusting its parameters:

| Parameter        | Description                                                                |
|------------------|----------------------------------------------------------------------------|
| `filePath`       | The path to the file where the version will be updated.                    |
| `regexp`         | The regular expression used to locate the version string.                  |
| `errorCodeDelta` | Optional delta value to ensure unique error codes across multiple modules. |

---

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository.
2. Create a new branch for your feature or bugfix.
3. Submit a pull request with a clear description of your changes.

Ensure that your code adheres to the project's coding standards and includes appropriate tests.

---

## Testing

Run the following commands to test the package:

```bash
composer test
```

Generate a code coverage report:

```bash
composer coverage
```

Perform static analysis:

```bash
composer stan
```

Check coding standards:

```bash
composer fixer
```

Run all checks at once:

```bash
composer check
```

---

## License

This package is licensed under the MIT License. See the [LICENSE](LICENSE.md) file for details.

---

## Useful Links

- [voral/version-increment](https://github.com/Voral/vs-version-incrementor): The main package this extension integrates
  with.