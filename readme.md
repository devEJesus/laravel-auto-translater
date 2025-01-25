# Laravel Auto Translator Package

A Laravel package that automatically translates content based on user input. This package allows users to specify a source language (`--from`) and a target language (`--to`), supporting multiple target languages by separating them with commas (e.g., `pt,es`). The package uses Deepl API for translation, requiring an API key to work.

## Installation

1. **Install the package** via Composer:
```   
composer require your-vendor/auto-translator
```

2. **Add the API key to your `.env` file**:
   To use Deepl API, you need to obtain an API key from Deepl (https://www.deepl.com/pro) and set it in your `.env` file.
    ```
   TRANSLATE_API_KEY=your_deepl_api_key
   ```

3. **Publish the package configuration (optional)**:
   If you want to customize the configuration, publish the package configuration file.
    ```
   php artisan vendor:publish --provider="YourVendor\AutoTranslator\AutoTranslatorServiceProvider"
   ```

## Usage

You can use the package with the following Artisan command:

```
php artisan translator:run --from=<source_language> --to=<target_languages>
```

### Parameters

- `--from`: The source language code (e.g., `en` for English, `de` for German).
- `--to`: The target language(s) for translation. You can specify multiple target languages by separating them with commas (e.g., `pt,es` for Portuguese and Spanish).

### Example

To translate from English (`en`) to Portuguese (`pt`) and Spanish (`es`):
```
php artisan translator:run --from=en --to=pt,es
```

This command will automatically translate your content from English to both Portuguese and Spanish using Deepl API.

## Requirements

- Deepl API Key: You need to create an account on Deepl and generate an API key. Place the key in your `.env` file under the variable `TRANSLATE_API_KEY`.

- Laravel 8 or higher.

## Notes

- The translation will be done based on the API capabilities of Deepl. If Deepl API has any rate limits or issues, the translation process might be delayed.
- The `--from` and `--to` languages should use standard ISO language codes (e.g., `en`, `es`, `de`, etc.).
- If multiple target languages are provided, the content will be translated and saved for each target language specified.

## License

This package is open source and available under the [MIT License](LICENSE).
