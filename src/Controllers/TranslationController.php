<?php

namespace Zubs\Translator\Controllers;

use Illuminate\Http\Request;
use Zubs\Translator\Translate;

class TranslationController
{
    public function indexCodes(Translate $translator): array
    {
        return $translator->getLanguageCodes();
    }

    public function index(Request $request, Translate $translator): array
    {
        return $translator->getLanguages($request->get('target', 'en'));
    }

    public function detectLanguage(Request $request, Translate $translator): string
    {
        return $translator->detectLanguage($request->get('text'));
    }

    public function translate(Request $request, Translate $translator): string
    {
        return $translator->translate($request->get('text'), $request->get('target', 'en'), $request->get('source'));
    }
}
