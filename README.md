# Zubs/Translator
A simple Laravel package that can translate between languages.

## Installation
You can install this package via composer using this command:
```bash
composer require zubs/translator
```

Then, the service provider is automatically registered. 

## Usage

### Methods
This package provides the `Zubs\Translator\Translate` and that provides the following methods:
- [`getLanguages()`](#get-all-available-languages-with-getlanguages)
- [`getLanguageCodes()`](#get-the-language-codes-of-all-available-languages-with-getlanguagecodes)
- [`detectLanguage()`](#detect-the-language-of-a-string-with-detectlanguage)
- [`translate()`](#translate-a-string-with-translate)

> All the methods cache results for better performances. The cache defaults to 24hrs but can be changed by passing an extra parameter to the methods.

#### Get all available languages with `getLanguages()`
This function definition looks like this:
```php
public function getLanguages(string $target = 'en', int $ttl = self::DEFAULT_CACHE_TIME): array
```

The `target` parameter is optional and defaults to `en`. This is the language that the returned array will be translated to.
The `ttl` is used to set the time that the cache will be stored for. It defaults to 1 day.

The function returns an array of all available languages in the format, `code => language`, like this:
```php
[
    'en' => 'English',
    'fr' => 'French',
    'es' => 'Spanish',
]
```

#### Get the language codes of all available languages with `getLanguageCodes()`
This function definition looks like this:
```php 
public function getLanguageCodes(int $ttl = self::DEFAULT_CACHE_TIME): array
```

This function takes just one parameter, `ttl`, and returns the same array as the `getLanguages()` function, but without the language names. Like this:
```php
[
    'en',
    'fr',
    'es',
]
```

The `ttl` is used to set the time that the cache will be stored for. It defaults to 1 day.

#### Detect the language of a string with `detectLanguage()`
This function definition looks like this:
```php
public function detectLanguage(string $text, int $ttl = self::DEFAULT_CACHE_TIME): string
```

This function takes a mandatory string, `text` as a parameter. It also takes an optional parameter, `ttl`.
The `ttl` is used to set the time that the cache will be stored for. It defaults to 1 day.
The function returns the language code of the language that the string is written in. Like this:
```php
detectLanguage('Hello world!'); // returns 'en'
```

#### Translate a string with `translate()`
This function definition looks like this:
```php
public function translate
(
    string $text,
    string $to,
    string $from = null,
    int $ttl = self::DEFAULT_CACHE_TIME
): string
```

This function takes a mandatory string, `text`, as a parameter and returns the translated string. 
The `to` parameter is the language code of the language that the string should be translated to. 
The `from` parameter is the language code of the language that the string is written in. 
If the `from` parameter is not provided, the function will try to detect the language of the string.
The `ttl` is used to set the time that the cache will be stored for. It defaults to 1 day.

The function can be used like this:
```php
translate('Hello world!', 'fr'); // returns 'Bonjour le monde!'

translate('Hello world!', 'fr', 'en'); // returns 'Bonjour le monde!'
```

### API
The package also exposes a few API endpoints that can be used to achieve the same results as the methods above.
- `GET /languages` - returns all available languages
- `GET /language/codes` - returns the language codes of all available languages
- `POST /detect-language` - returns the language code of the language that a string is written in
- `POST /translate` - returns a translated string
