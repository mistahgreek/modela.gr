<?php

return [
    /*
    |--------------------------------------------------------------------------
    | File Upload Configuration
    |--------------------------------------------------------------------------
    */
    'upload' => [
        'max_size' => env('MAX_UPLOAD_SIZE', 104857600), // 100MB in bytes
        'allowed_model_extensions' => explode(',', env('ALLOWED_MODEL_EXTENSIONS', 'stl,obj,3mf')),
        'allowed_image_extensions' => explode(',', env('ALLOWED_IMAGE_EXTENSIONS', 'jpg,jpeg,png,webp')),
    ],

    /*
    |--------------------------------------------------------------------------
    | 3D Model Processing Configuration
    |--------------------------------------------------------------------------
    */
    'processing' => [
        'enable_server_thumbnails' => env('ENABLE_SERVER_SIDE_THUMBNAILS', false),
        'thumbnail_width' => env('THUMBNAIL_WIDTH', 800),
        'thumbnail_height' => env('THUMBNAIL_HEIGHT', 600),
    ],

    /*
    |--------------------------------------------------------------------------
    | Pricing Configuration
    |--------------------------------------------------------------------------
    */
    'pricing' => [
        'default_currency' => env('DEFAULT_CURRENCY', 'EUR'),
        'vat_rate' => env('VAT_RATE', 0.24), // Greek VAT 24%
        'vat_enabled' => env('VAT_ENABLED', true),
        'shipping_enabled' => env('SHIPPING_ENABLED', true),
        'default_shipping_cost' => env('DEFAULT_SHIPPING_COST', 5.00),
        
        // Printing time estimation factors
        'time_factor_by_layer_height' => [
            '0.12' => 1.5,  // Fine quality takes 50% more time
            '0.20' => 1.0,  // Normal quality baseline
            '0.28' => 0.7,  // Fast quality 30% less time
        ],
        
        // Waste/support material factor
        'material_waste_factor' => 1.15, // 15% waste for supports, purge, etc.
    ],

    /*
    |--------------------------------------------------------------------------
    | Stripe Configuration
    |--------------------------------------------------------------------------
    */
    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'currency' => env('STRIPE_CURRENCY', 'eur'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Configuration
    |--------------------------------------------------------------------------
    */
    'admin' => [
        'email' => env('ADMIN_EMAIL', 'admin@modela.gr'),
        'panel_path' => env('ADMIN_PANEL_PATH', 'admin'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Configuration
    |--------------------------------------------------------------------------
    */
    'rate_limits' => [
        'per_minute' => env('RATE_LIMIT_PER_MINUTE', 60),
        'upload_per_hour' => env('UPLOAD_RATE_LIMIT_PER_HOUR', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    */
    'security' => [
        'signed_url_expiration' => env('SIGNED_URL_EXPIRATION', 3600), // 1 hour in seconds
    ],
];
