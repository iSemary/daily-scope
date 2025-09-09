<?php

namespace Modules\Language\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Modules\Language\Services\LanguageService;

class LanguageController extends ApiController
{
    private LanguageService $languageService;

    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    public function index(): JsonResponse
    {
        $languages = $this->languageService->list();
        return $this->return(200, "Languages fetched successfully", ['languages' => $languages]);
    }

    public function show(string $code): JsonResponse
    {
        $language = $this->languageService->showByCode($code);
        if (!$language) {
            return $this->return(404, "Language not found");
        }
        return $this->return(200, "Language fetched successfully", ['language' => $language]);
    }
}
