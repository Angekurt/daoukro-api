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

    // FCM HTTP v1 (l'ancienne API legacy fcm.googleapis.com/fcm/send est
    // fermée par Google) — nécessite un compte de service Firebase.
    'fcm' => [
        'project_id' => env('FCM_PROJECT_ID'),
        'credentials_path' => env('FCM_CREDENTIALS_PATH', storage_path('app/firebase-service-account.json')),
    ],

    // Connexion Google (app mobile) — client_id "Web" auto-créé par Firebase
    // dès que Google est activé comme fournisseur dans Authentication >
    // Sign-in method. C'est CE client_id (pas celui de l'app Android) qui
    // doit être utilisé côté Flutter comme `serverClientId`, pour que
    // l'idToken renvoyé ait bien cette audience-là et soit vérifiable ici.
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
    ],

];
