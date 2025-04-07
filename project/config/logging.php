<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => env('LOG_LEVEL', 'critical'),
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

        'whatsapp' => [
            'driver' => 'single',
            'path' => storage_path('logs/whatsapp-'.date('Y-m').'.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'scheduler' => [
            'driver' => 'single',
            'path' => storage_path('logs/scheduler-'.date('Y-m').'.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'bgen' => [
            'driver' => 'single',
            'path' => storage_path('logs/bgen-'.date('Y-m').'.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'mobile' => [
            'driver' => 'daily',
            'channels' => ['daily'],
            'path' => storage_path('logs/mobile.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
        ],

        'mobile_request' => [
            'driver' => 'single',
            'path' => storage_path('logs/mobile-request-'.date('Y-m').'.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'mobile_approval' => [
            'driver' => 'single',
            'path' => storage_path('logs/mobile-approval-'.date('Y-m').'.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'email' => [
            'driver' => 'single',
            'path' => storage_path('logs/email-'.date('Y-m').'.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'function' => [
            'driver' => 'single',
            'path' => storage_path('logs/function-'.date('Y-m').'.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],
    ],

];
