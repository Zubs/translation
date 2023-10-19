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
        $response = json_decode($response->body(), true);
        $languages = $response['data']['languages'];

        return array_map(function ($language) {
            return [$language['language'] => $language['name']];
        }, $languages);
    }

    /**
     * Detects what language a given string is written in
     *
     * @param string $text Text to detect language
     * @return string Language code
     */
    public function detectLanguage(string $text): string
    {
        $response = Http::asForm()->withHeaders($this->getHeaders())->post($this->getURL('detect'), [
            'q' => $text
        ]);
        $response = json_decode($response->body(), true);

        return $response['data']['detections'][0][0]['language'];
    }

    /**
     * Translates a string from one language to another
     *
     * @param string $text Text to translate
     * @param string $to Language code to translate to
     * @param string|null $from Language code to translate from, if null will auto-detect
     * @return string Translated text
     */
    public function translate(string $text, string $to, string $from = null): string
    {
        $body = [
            'q' => $text,
            'target' => $to
        ];

        if (!is_null($from)) {
            $body['source'] = $from;
        }

        $response = Http::asForm()->withHeaders($this->getHeaders())->post($this->getURL(), $body);
        $response = json_decode($response->body(), true);

        return $response['data']['translations'][0]['translatedText'];
    }
}
