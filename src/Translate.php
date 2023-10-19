<?php

namespace Zubs\Translator;

use Exception;
use Illuminate\Support\Facades\Http;

class Translate
{
    private string $baseUrl = "https://google-translate1.p.rapidapi.com/language/translate/v2/";
    private array $headers = [
        'Accept-Encoding' => 'application/gzip',
		'X-RapidAPI-Key' => '5d30e801famshf83dd63f5d9a32bp110782jsnf912ec63bdee', // TODO: Move to .env
		'X-RapidAPI-Host' => 'google-translate1.p.rapidapi.com'
    ];

    public function __construct()
    {
    }

    private function getHeaders(array $headers = []): array
    {
        return array_merge($this->headers, $headers);
    }

    private function getURL(string $url = ''): string
    {
        return $this->baseUrl . $url;
    }

    /**
     * Get all languages available as just the language code
     *
     * @return array List of language codes
     * @throws Exception
     */
    public function getLanguageCodes(): array
    {
        $response = Http::withHeaders($this->getHeaders())->get($this->getURL('languages'));

        $response = json_decode($response->body(), true);
        $languages = $response['data']['languages'];

        return array_map(function ($language) {
            return $language['language'];
        }, $languages);
    }

    /**
     * Get all languages available as an array of language code => language name
     *
     * @param string $target Language code to translate the language names to, default is 'en'
     * @return array List of language code => language name
     */
    public function getLanguages(string $target = 'en'): array
    {
        $response = Http::withHeaders($this->getHeaders())->get($this->getURL('languages?target=' . $target));

        // TODO: Return only the language codes and names

        return [$response->body()];
    }

    /**
     * Detects what language a given string is written in
     *
     * @param string $text Text to detect language
     * @return string Language code
     */
    public function detectLanguage(string $text): string
    {
        $response = Http::withHeaders($this->getHeaders([
            'content-type' => 'application/x-www-form-urlencoded'
        ]))->post($this->getURL('detect'), [
            'form_params' => [
                'q' => $text
            ],
        ]);

        return $response->body();
    }

    /**
     * Translates a string from one language to another
     *
     * @param string $text Text to translate
     * @param string $to Language code to translate to
     * @param string $from Language code to translate from, default is 'en'
     * @return string Translated text
     */
    public function translate(string $text, string $to, string $from = 'en'): string
    {
        $response = Http::withHeaders($this->getHeaders([
            'content-type' => 'application/x-www-form-urlencoded'
        ]))->post($this->getURL(), [
            'form_params' => [
                'q' => $text,
                'target' => $to,
                'source' => $from
            ],
        ]);

        return $response->body();
    }
}
