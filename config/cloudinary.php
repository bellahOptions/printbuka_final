<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    |
    | Credentials for the Cloudinary image management service.
    | All image uploads are routed through Cloudinary for CDN delivery.
    |
    */

    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
    'api_key' => env('CLOUDINARY_API_KEY'),
    'api_secret' => env('CLOUDINARY_API_SECRET'),
    'secure' => env('CLOUDINARY_SECURE', true),

    /*
    |--------------------------------------------------------------------------
    | Default upload settings
    |--------------------------------------------------------------------------
    */
    'default_folder' => env('CLOUDINARY_DEFAULT_FOLDER', 'printbuka'),
    'default_transformations' => [
        'quality' => 'auto',
        'fetch_format' => 'auto',
    ],
];
