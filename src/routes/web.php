<?php

use Illuminate\Support\Facades\Route;
use Zubs\Translator\Controllers\TranslationController;

//Route::controller(TranslationController::class);

Route::group([
    'prefix' => 'languages',
    'as' => 'languages.',
    'controller' => TranslationController::class
], function () {
    Route::get('', 'index'); // ?target to specify target language
    Route::get('codes', 'indexCodes');
    Route::post('detect', 'detectLanguage');
    Route::post('translate', 'translate');
});
