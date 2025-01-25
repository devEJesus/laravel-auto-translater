<?php

namespace LaravelAutoTranslator\Core;

use GuzzleHttp\Client;

class Api
{
    private string $key;

    private Client $httpClient;

    public function __construct()
    {
        $key = env('TRANSLATE_API_KEY');
        if (is_string($key)) {
            $this->key = $key;
        } else {
            throw new \Exception('No api key provided');
        }
        $this->httpClient = new Client([
            'base_uri' => 'https://api-free.deepl.com/v2/',
        ]);
    }

    public function translate(string $htmlContent, string $sourceLang, string $targetLang): string
    {
        $params = [
            'auth_key' => $this->key,
            'text' => $htmlContent,
            'source_lang' => strtoupper($sourceLang),
            'target_lang' => strtoupper($targetLang),
            'tag_handling' => 'html',
        ];

        $response = $this->httpClient->post('translate', [
            'form_params' => $params,
        ]);

        $responseBody = json_decode((string) $response->getBody(), true);

        if (
            is_array($responseBody) &&
            isset($responseBody['translations']) &&
            is_array($responseBody['translations']) &&
            isset($responseBody['translations'][0]) &&
            is_array($responseBody['translations'][0]) &&
            isset($responseBody['translations'][0]['text'])
        ) {

            return $responseBody['translations'][0]['text'];
        }

        throw new \RuntimeException('Translation failed. Response: '.json_encode($responseBody));
    }
}
