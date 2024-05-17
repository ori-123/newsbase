<?php

namespace includes;

class Logger
{
    private static string $backendLogFile = __DIR__ . '/../logs/backend_logs.log';
    private static string $frontendLogFile = __DIR__ . '/../logs/frontend_logs.log';

    public static function backend_log($level, $message): void
    {
        $formattedMessage = date('[Y-m-d H:i:s]') . "[$level] $message" . PHP_EOL;
        error_log($formattedMessage, 3, self::$backendLogFile);
    }

    public static function backend_info($message): void
    {
        self::backend_log('INFO', $message);
    }

    public static function backend_error($message): void
    {
        self::backend_log('ERROR', $message);
    }

    public static function frontend_log($level, $message): void
    {
        $formattedMessage = date('[Y-m-d H:i:s]') . "[$level] $message" . PHP_EOL;
        error_log($formattedMessage, 3, self::$frontendLogFile);
    }

    public static function frontend_info($message): void
    {
        self::frontend_log('INFO', $message);
    }

    public static function frontend_error($message): void
    {
        self::frontend_log('ERROR', $message);
    }
}