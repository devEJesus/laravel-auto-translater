<?php

namespace LaravelAutoTranslator\Core;

use LaravelAutoTranslator\Storage\LangDirectory;

use function Termwind\render;

/**
 * @template TItem
 */
final readonly class Translator
{
    private LangDirectory $langDirectory;

    private Api $api;

    private bool $createIfNotExist;

    /**
     * @param  array<string,string>  $options
     */
    public function __construct(
        private array $options,
    ) {
        $this->langDirectory = new LangDirectory;
        $this->api = new Api;
        $this->createIfNotExist = true;
    }

    public function run(): void
    {
        $from = $this->options['from'];
        $to = explode(',', $this->options['to']);

        if (! $this->validate($from, $to)) {
            return;
        }

        $fromFiles = $this->langDirectory->getFiles($from);

        foreach ($fromFiles as $file) {
            $translations = include $file;

            $transCodified = $this->encodeParams($translations);

            $html = Html::fromArray($transCodified);
            if (! $html) {
                throw new \Exception('Error while parse array into html.');
            }

            foreach ($to as $lang) {
                $response = $this->api->translate($html, $from, $lang);

                $transArray = Html::toArray($response);

                $transArray = $this->decodeParams($transArray);

                $content = "<?php\n\nreturn ".var_export($transArray, true).";\n";
                $langPath = $this->langDirectory->getPath($lang);

                file_put_contents($langPath.'/'.$file->getFileName(), $content);
                $this->render('green', 'Success', "  {$file->getFileName()} translated successfully to {$lang}");
            }

        }
    }

    private function decodeParams(array $translations): array
    {
        $processed = [];

        foreach ($translations as $key => $value) {
            if (is_array($value)) {
                // Recursively decode nested arrays
                $processed[$key] = $this->decodeParams($value);
            } else {
                // Decode <param> tags back into :param format
                $processed[$key] = preg_replace_callback(
                    '/<param>(\w+)<\/param>/',  // Regex to match <param>...</param>
                    function ($matches) {
                        return ":{$matches[1]} ";  // Replace with :param
                    },
                    $value
                );
            }
        }

        return $processed;
    }

    private function encodeParams(array $translations): array
    {
        $processed = [];

        foreach ($translations as $key => $value) {
            if (is_array($value)) {

                $processed[$key] = $this->encodeParams($value);
            } else {
                $replace = (string) preg_replace_callback(
                    '/:(\w+)/',
                    function ($matches) {
                        return "<param>{$matches[1]}</param>";
                    },
                    $value
                );
                $processed[$key] = htmlspecialchars_decode($replace);
            }
        }

        return $processed;
    }

    /**
     * Validate if the files of langs exists.
     *
     * @param  array<int, string>  $to
     */
    private function validate(string $from, array $to): bool
    {
        $valid = true;
        $validate['langDirectory'] = $this->langDirectory->exists();
        $validate['fromDirectory'] = $this->langDirectory->exists($from);
        $validate['toDirectory'] = [];
        foreach ($to as $lang) {
            $validate['toDirectory'][$lang] = $this->langDirectory->exists($lang, true);
        }

        if (! $validate['langDirectory']) {
            $valid = false;
            $this->render('red', 'Error', "The directory lang doesn't exist in the project.");
        }
        if (! $validate['fromDirectory']) {
            $valid = false;
            $this->render('red', 'Error', "The directory {$from} doesn't exist.");
        }

        foreach ($validate['toDirectory'] as $key => $to) {
            if (! $to) {
                if ($this->createIfNotExist) {
                    $this->langDirectory->create($key);
                    $this->render('orange', 'Created', "The directory of language {$key} was created.");
                } else {
                    $valid = false;
                    $this->render('red', 'Error', "The directory of language {$key} doesn't exist.");
                }

            }
        }

        return $valid;
    }

    private function render(string $color = 'green', string $subject = '', string $body = ''): void
    {
        render("<div class='mb-1'>
            <div class='px-1 bg-{$color}-600'>{$subject}</div>
            <em class='ml-1'>
                {$body}
            </em>
        </div>");
    }
}
