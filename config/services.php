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
        'token' => env('POSTMARK_TOKEN'),
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

    'gemini' => [
    'key' => env('GEMINI_API_KEY'),
	],

    'clicksign' => [
        'token'   => env('CLICKSIGN_ACCESS_TOKEN', ''),
        'sandbox' => env('CLICKSIGN_SANDBOX', true),
    ],

    'pix' => [
        'chave'  => env('PIX_CHAVE', ''),
        'nome'   => env('PIX_NOME', 'ESCRITORIO'),
        'cidade' => env('PIX_CIDADE', 'SAO PAULO'),
    ],

    'twilio' => [
        'sid'           => env('TWILIO_ACCOUNT_SID', ''),
        'token'         => env('TWILIO_AUTH_TOKEN', ''),
        'from_whatsapp' => env('TWILIO_WHATSAPP_FROM', ''),
        'from_sms'      => env('TWILIO_SMS_FROM', ''),
        'canal_padrao'  => env('TWILIO_CANAL_PADRAO', 'whatsapp'),
    ],

];
