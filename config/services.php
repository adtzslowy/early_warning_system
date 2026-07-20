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

    'iot' => [
        'base_url' => env('IOT_KETAPANG_URL'),
    ],

    /*
    | Data maritim BMKG (Prakiraan Cuaca Perairan). Dipakai oleh generator data
    | sintetis (mock IoT untuk demo/skripsi) sebagai sinyal penggerak angin &
    | gelombang. Atribusi sumber "BMKG" WAJIB dicantumkan.
    |
    | `perairan_url` = direktori file prakiraan per area; daftar area diambil
    | dari `{parent}/perairan_list`. `area_code` = kode wilayah perairan
    | (D.11 = Perairan Ketapang). Kosong = generator pakai baseline.
    */
    'bmkg' => [
        'perairan_url' => env('BMKG_PERAIRAN_URL', 'https://peta-maritim.bmkg.go.id/public_api/perairan'),
        'cache_ttl' => (int) env('BMKG_CACHE_TTL', 10800), // detik (3 jam)
        'area_code' => env('BMKG_DEFAULT_AREA_CODE', ''),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI')
    ],

    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'chat_id' => env('TELEGRAM_CHAT_ID'),
        'api_url' => 'https://api.telegram.org',
        'timeout' => 10,
    ],

];
