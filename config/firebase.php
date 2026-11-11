<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Cloud Messaging
    |--------------------------------------------------------------------------
    | FCM_SERVER_KEY  - Legacy server key from Firebase Console
    |                   (Project Settings > Cloud Messaging > Server key)
    | VITE_* vars     - Used by the front-end FCM SDK
    */
    'server_key' => env('FCM_SERVER_KEY', ''),
];