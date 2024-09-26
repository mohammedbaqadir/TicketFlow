<?php
    declare( strict_types = 1 );

    return [
        'max_attempts' => env( 'AUTH_MAX_ATTEMPTS', 5 ),
        'lockout_duration' => env( 'AUTH_LOCKOUT_DURATION', 5 ),
        'global_limit' => env( 'AUTH_GLOBAL_LIMIT', 60 ),
        'auth_limit' => env( 'AUTH_LIMIT', 5 ),
        'email_limit' => env( 'AUTH_EMAIL_LIMIT', 5 ),
        'ip_limit' => env( 'AUTH_IP_LIMIT', 20 ),
    ];