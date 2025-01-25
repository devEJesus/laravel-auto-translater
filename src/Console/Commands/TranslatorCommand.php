<?php

namespace LaravelAutoTranslator\Console\Commands;

use Illuminate\Console\Command;
use LaravelAutoTranslator\Core\Translator;

class TranslatorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translator:run {--from=} {--to=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Translate the files';

    /**
     * The required options and their corresponding methods.
     *
     * @var array<string,string>
     */
    protected array $requiredOptions = [
        'from' => 'askFrom',
        'to' => 'askTo',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // Ensure all required options are set
        $options = $this->ensureRequiredOptions($this->options());

        $this->info("Translating from: {$options['from']}");
        $this->info("Translating to: {$options['to']}");

        $service = new Translator($options);
        $service->run();
    }

    /**
     * Ensure that all required options are set, either from input or prompted.
     *
     * @param  array<string,string>  $options
     * @return array<string,string>
     */
    private function ensureRequiredOptions(array $options): array
    {
        foreach ($this->requiredOptions as $option => $method) {
            if ($options[$option] == null) {
                $options[$option] = $this->$method();
            }
        }

        return $options;
    }

    /**
     * Ask the user for the source language.
     */
    private function askFrom(): string
    {
        return strval($this->ask('From what language do you want to translate?').'');
    }

    /**
     * Ask the user for the target language(s).
     */
    private function askTo(): string
    {
        return strval($this->ask('To what languages do you want to translate? For multiple choices, separate them by a comma.').'');
    }
}
