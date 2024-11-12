<?php

return [
    'paths' => [
        // The directory where the generated documentation will be stored
        'output' => storage_path('api-docs'),
        // The directory where your annotated files are located
        'annotations' => [
            app_path('Http/Controllers'),
        ],
    ],
];