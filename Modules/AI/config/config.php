<?php

return [
    'name' => 'AI',
    'api_key' => env('DEEPSEEK_API_KEY', ''),
    'model' => env('DEEPSEEK_MODEL', 'deepseek-chat'),
    'max_tokens' => env('DEEPSEEK_MAX_TOKENS', 4096),
    'temperature' => env('DEEPSEEK_TEMPERATURE', 0.7),
];