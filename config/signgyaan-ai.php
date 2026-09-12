<?php

return [
    'teacher' => [
        'provider' => env('SIGNGYAAN_AI_PROVIDER', 'openai'),
        'model' => env('SIGNGYAAN_AI_MODEL', 'gpt-5.6-luna'),
        'timeout' => (int) env('SIGNGYAAN_AI_TIMEOUT', 45),
        'max_prompt_characters' => 4000,
        'max_context_characters' => 2000,
    ],
];
