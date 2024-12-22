<?php
    declare( strict_types = 1 );

    use Illuminate\Support\Env;

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

        'jitsi' => [
            'vpaas_magic_cookie' => Env::getOrFail( 'JITSI_VPAAS_MAGIC_COOKIE' ),
        ],

        'openrouter' => [
            'default_model' => 'meta-llama/llama-3.1-70b-instruct:free',
            'api_key' => Env::getOrFail( 'OPENROUTER_API_KEY' ),
        ],


    ];