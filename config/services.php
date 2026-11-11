<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'fonnte' => [
        'key' => env('FONNTE_API_KEY', ''),
        'base_url' => env('FONNTE_BASE_URL', 'https://api.fonnte.com'),
        'driver' => env('WA_GATEWAY_DRIVER', env('FONNTE_API_KEY') ? 'fonnte' : 'mock'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase
    |--------------------------------------------------------------------------
    | FCM_SERVER_KEY — Legacy FCM server key (Project Settings > Cloud Messaging)
    | VITE_FIREBASE_* — Web SDK config exposed to the browser via Vite
    */
    'firebase' => [
        'server_key'           => env('FCM_SERVER_KEY', ''),
        'api_key'              => env('FIREBASE_API_KEY', ''),
        'auth_domain'          => env('FIREBASE_AUTH_DOMAIN', ''),
        'project_id'           => env('FIREBASE_PROJECT_ID', ''),
        'storage_bucket'       => env('FIREBASE_STORAGE_BUCKET', ''),
        'messaging_sender_id'  => env('FIREBASE_MESSAGING_SENDER_ID', ''),
        'app_id'               => env('FIREBASE_APP_ID', ''),
        'vapid_key'            => env('FIREBASE_VAPID_KEY', ''),
    ],

];
