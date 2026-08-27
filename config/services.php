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

    'appypay' => [
        'client_id'           => env('APPYPAY_CLIENT_ID'),
        'client_secret'       => env('APPYPAY_CLIENT_SECRET'),
        'resource'            => env('APPYPAY_RESOURCE'),
        'mode'                => env('APPYPAY_MODE', 'sandbox'), // 'sandbox' ou 'live'
        'base_url'            => env('APPYPAY_BASE_URL', 'https://gwy-api-tst.appypay.co.ao'),
        'auth_url'            => env('APPYPAY_AUTH_URL', 'https://login.microsoftonline.com/appypaydev.onmicrosoft.com/oauth2/token'),
        // Identificadores de "payment method" fornecidos pela AppyPay para cada meio de pagamento
        'payment_method_gpo'  => env('APPYPAY_PAYMENT_METHOD_GPO'), // Multicaixa Express (telefone)
        'payment_method_ref'  => env('APPYPAY_PAYMENT_METHOD_REF'), // Referência (ATM/Multicaixa/Internet Banking)
        'entity'              => env('APPYPAY_ENTITY', '00348'),
        // Segredo partilhado para validar o webhook — a confirmar com a AppyPay
        // (operacao.pay@appy.co.ao) quando o webhook for registado do lado deles.
        'webhook_secret'      => env('APPYPAY_WEBHOOK_SECRET', ''),
    ],

    'google_vision' => [
        'key' => env('GOOGLE_VISION_API_KEY'),
    ],

];
