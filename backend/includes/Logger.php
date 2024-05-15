<?php

namespace includes;

class Logger
{
    private static $logFile = __DIR__ . '/../logs/logfile.log';

    public static function log($level, $message)
    {
        $formattedMessage = date('[Y-m-d H:i:s]') . "[$level] $message" . PHP_EOL;
        error_log($formattedMessage, 3, self::$logFile);
    }

    public static function info($message)
    {
        self::log('INFO', $message);
    }

    public static function error($message) {
        self::log('ERROR', $message);
    }
}