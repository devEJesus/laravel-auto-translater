<?php

test('prompts for from and to languages when options are not provided', function () {
    $this->artisan('translator:run')
        ->expectsQuestion('From what language do you want to translate?', 'en')
        ->expectsQuestion('To what languages do you want to translate? For multiple choices, separate them by a comma.', 'fr')
        ->assertExitCode(0);
});

test('runs successfully with both from and to options provided', function () {
    $this->artisan('translator:run --from=es --to=fr,de')
        ->assertExitCode(0)
        ->expectsOutput('Translating from: es')
        ->expectsOutput('Translating to: fr,de');
});

test('prompts the user correctly when no options are provided', function () {
    $this->artisan('translator:run')
        ->expectsQuestion('From what language do you want to translate?', 'en')
        ->expectsQuestion('To what languages do you want to translate? For multiple choices, separate them by a comma.', 'fr')
        ->assertExitCode(0)
        ->expectsOutput('Translating from: en')
        ->expectsOutput('Translating to: fr');
});

test('propts the user correctly when only option are provided', function () {
    $this->artisan('translator:run --to=es')
        ->expectsQuestion('From what language do you want to translate?', 'en')
        ->assertExitCode(0)
        ->expectsOutput('Translating from: en')
        ->expectsOutput('Translating to: es');
});

test('prompts for valid from and to languages when the input is empty', function () {
    $this->artisan('translator:run --from= --to=')
        ->expectsQuestion('From what language do you want to translate?', 'en')
        ->expectsQuestion('To what languages do you want to translate? For multiple choices, separate them by a comma.', 'fr')
        ->assertExitCode(0)
        ->expectsOutput('Translating from: en')
        ->expectsOutput('Translating to: fr');
});
