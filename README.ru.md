# vinc-file-version-regexp

[EN](README.md)

![PHP Tests](https://github.com/Voral/vinc-file-version-regexp/actions/workflows/php.yml/badge.svg)
[![Code Coverage](https://scrutinizer-ci.com/g/Voral/vinc-file-version-regexp/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Voral/vinc-file-version-regexp/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Voral/vinc-file-version-regexp/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Voral/vinc-file-version-regexp/?branch=master)

Расширение для [voral/version-increment](https://github.com/Voral/vs-version-incrementor), позволяющее обновлять строки
версий в произвольных файлах с использованием регулярных выражений.

Этот инструмент позволяет автоматически обновлять номера версий в любых файлах (например, PHP, JSON, XML) во время
процесса версионирования. Он легко интегрируется с пакетом `voral/version-increment` и реагирует на
событие `BEFORE_VERSION_SET`, чтобы выполнять замены на основе предоставленного регулярного выражения.

---

## Основные возможности

- **Поддержка произвольных файлов**: Обновление строк версий в любом формате файлов с использованием гибких регулярных
  выражений.
- **Интеграция на основе событий**: Реагирует на событие `BEFORE_VERSION_SET` из `voral/version-increment`.
- **Обработка ошибок с уникальными кодами**: Предотвращает конфликты между несколькими расширениями за счет поддержки
  дельты кодов ошибок.
- **Гибкая настройка**: Определение собственных регулярных выражений для поиска строк версий в различных форматах.
- **Расширяемость**: Легкая интеграция в существующие рабочие процессы с минимальной настройкой.

---

## Установка

Установите пакет через Composer:

```bash
composer require --dev voral/vinc-file-version-regexp
```

---

## Использование

Чтобы использовать это расширение, настройте его в файле `.vs-version-increment.php`, добавив слушатель для
события `BEFORE_VERSION_SET`. Пример:

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

### Пояснение:

- **Путь к файлу**: Укажите путь к файлу, где должна быть обновлена строка версии (например, `./src/version.php`).
- **Регулярное выражение**: Укажите шаблон регулярного выражения для поиска строки версии. Шаблон должен включать:
    - Группа 1: Часть строки до версии.
    - Группа 2: Часть строки после версии.
- **Дельта кодов ошибок**: По желанию укажите значение дельты, чтобы обеспечить уникальность кодов ошибок при
  использовании нескольких расширений.

---

## Примеры использования

### 1. Обновление PHP-файла

Если ваш проект содержит файл `version.php` следующего содержания:

```php
<?php $version = "Version 1.0.0"; ?>
```

Расширение обновит его до:

```php
<?php $version = "Version 2.0.0"; ?>
```

### 2. Обновление JSON-файла

Для файла `config.json`:

```json
{
  "version": "1.0.0"
}
```

Расширение может обновить его до:

```json
{
  "version": "2.0.0"
}
```

---

## Обработка ошибок

Расширение предоставляет подробные сообщения об ошибках и уникальные коды ошибок для диагностики проблем:

- **FileNotFoundException**: Выбрасывается, если указанный файл не существует.
- **FileNotWritableException**: Выбрасывается, если файл недоступен для записи.
- **PatternNotFoundException**: Выбрасывается, если регулярное выражение не найдено в содержимом файла.

Коды ошибок смещаются настраиваемым значением `errorCodeDelta`, чтобы избежать конфликтов с другими расширениями.

---

## Параметры настройки

Вы можете настроить поведение расширения, изменив его параметры:

| Параметр         | Описание                                                                  |
|------------------|---------------------------------------------------------------------------|
| `filePath`       | Путь к файлу, где будет обновлена версия.                                 |
| `regexp`         | Регулярное выражение, используемое для поиска строки версии.              |
| `errorCodeDelta` | Необязательное значение дельты для обеспечения уникальности кодов ошибок. |

---

## Вклад в развитие

Вклады приветствуются! Пожалуйста, следуйте этим шагам:

1. Создайте форк репозитория.
2. Создайте новую ветку для вашей функции или исправления ошибки.
3. Отправьте pull request с четким описанием ваших изменений.

Убедитесь, что ваш код соответствует стандартам кодирования проекта и включает соответствующие тесты.

---

## Тестирование

Запустите следующие команды для тестирования пакета:

```bash
composer test
```

Сгенерировать отчет о покрытии кода:

```bash
composer coverage
```

Выполнить статический анализ:

```bash
composer stan
```

Проверить соответствие стандартам кодирования:

```bash
composer fixer
```

Запустить все проверки одновременно:

```bash
composer check
```

---

## Лицензия

Этот пакет лицензирован под лицензией MIT. Подробности см. в файле [LICENSE](LICENSE.md).

---

## Полезные ссылки

- [voral/version-increment](https://github.com/Voral/vs-version-incrementor): Основной пакет, с которым интегрируется
  это расширение.