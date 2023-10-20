<?php

namespace Zubs\Translator;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class Translate
{
    private string $baseUrl = "https://google-translate1.p.rapidapi.com/language/translate/v2/";
    private array $headers = [
        'Accept-Encoding' => 'application/gzip',
		'X-RapidAPI-Key' => '5d30e801famshf83dd63f5d9a32bp110782jsnf912ec63bdee', // TODO: Move to .env
		'X-RapidAPI-Host' => 'google-translate1.p.rapidapi.com'
    ];
    private CONST DEFAULT_CACHE_TIME = 60 * 60 * 24;

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
     * Get all languages available as just the language codes
     *
     * @param int $ttl Time to live in seconds - default 24hrs
     *
     * @return array List of language codes
     */
    public function getLanguageCodes(int $ttl = self::DEFAULT_CACHE_TIME): array
    {
        return Cache::remember('translator:language_codes', $ttl, function () {
            $response = Http::withHeaders($this->getHeaders())->get($this->getURL('languages'));
            $response = json_decode($response->body(), true);
            $languages = $response['data']['languages'];

            return array_map(function ($language) {
                return $language['language'];
            }, $languages);
        });
    }

    /**
     * Get all languages available as an array of language code => language name
     *
     * @param string $target Language code to translate the language names to - default 'en'
     * @param int $ttl Time to live in seconds - default 24hrs
     *
     * @return array List of language code => language name
     */
    public function getLanguages(string $target = 'en', int $ttl = self::DEFAULT_CACHE_TIME): array
    {
        $cacheKey = 'translator:languages:' . $target;

        return Cache::remember($cacheKey, $ttl, function () use ($target) {
            $response = Http::withHeaders($this->getHeaders())->get($this->getURL('languages?target=' . $target));
            $response = json_decode($response->body(), true);
            $languages = $response['data']['languages'];

            return array_map(function ($language) {
                return [$language['language'] => $language['name']];
            }, $languages);
        });
    }

    /**
     * Detects what language a given string is written in
     *
     * @param string $text Text to detect language
     * @param int $ttl Time to live in seconds - default 24hrs
     *
     * @return string Language code
     */
    public function detectLanguage(string $text, int $ttl = self::DEFAULT_CACHE_TIME): string
    {
        $cacheKey = 'translator:detections:' . $text;

        return Cache::remember($cacheKey, $ttl, function () use ($text) {
            $response = Http::asForm()->withHeaders($this->getHeaders())->post($this->getURL('detect'), [
                'q' => $text
            ]);
            $response = json_decode($response->body(), true);

            return $response['data']['detections'][0][0]['language'];
        });
    }

    /**
     * Translates a string from one language to another
     *
     * @param string $text Text to translate
     * @param string $to Language code to translate to
     * @param string|null $from Language code to translate from, if null will auto-detect
     * @param int $ttl Time to live in seconds - default 24hrs
     *
     * @return string Translated text
     */
    public function translate
    (
        string $text,
        string $to,
        string $from = null,
        int $ttl = self::DEFAULT_CACHE_TIME
    ): string
    {
        $cacheKey = 'translator:translations:' . $text . ':to:' . $to . ':from:' . $from;

        return Cache::remember($cacheKey, 60 * 60 * 24, function () use ($text, $to, $from) {
            $body = [
                'q' => $text,
                'target' => $to
            ];

            if (!is_null($from)) {
                $body['source'] = $from;
            }

            $response = Http::asForm()->withHeaders($this->getHeaders())->post(
                $this->getURL(),
                $body
            );
            $response = json_decode($response->body(), true);

            return $response['data']['translations'][0]['translatedText'];
        });
    }
}
