<?php

use App\Services\AI\FakeProjectReportGenerator;

return [
    'enabled' => env('AI_REPORTS_ENABLED', false),
    'provider' => env('AI_PROVIDER', 'fake'),
    'model' => env('AI_MODEL', 'fake-local'),
    'api_key' => env('AI_API_KEY'),
    'prompt_version' => env('AI_PROMPT_VERSION', 'v1'),
    'generator' => env('AI_REPORT_GENERATOR', FakeProjectReportGenerator::class),
];
