<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Application Logger.
 */
class Logger
{
    private static string $logPath;

    public static function init(): void
    {
        self::$logPath = dirname(__DIR__, 2) . '/storage/logs/';
        if (!is_dir(self::$logPath)) {
            mkdir(self::$logPath, 0755, true);
        }
    }

    /**
     * Log an info message.
     */
    public static function info(string $message, array $context = []): void
    {
        self::log('INFO', $message, $context);
    }

    /**
     * Log a warning.
     */
    public static function warning(string $message, array $context = []): void
    {
        self::log('WARNING', $message, $context);
    }

    /**
     * Log an error.
     */
    public static function error(string $message, array $context = []): void
    {
        self::log('ERROR', $message, $context);
    }

    /**
     * Log a debug message.
     */
    public static function debug(string $message, array $context = []): void
    {
        if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
            self::log('DEBUG', $message, $context);
        }
    }

    /**
     * Write a log entry.
     */
    private static function log(string $level, string $message, array $context): void
    {
        if (!isset(self::$logPath)) {
            self::init();
        }

        $date = date('Y-m-d');
        $time = date('Y-m-d H:i:s');
        $file = self::$logPath . $date . '.log';

        $contextStr = !empty($context) ? ' ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
        $userId = (new Session())->get('user_id') ?? 'guest';

        $logLine = "[{$time}] [{$level}] [User:{$userId}] [IP:{$ip}] {$message}{$contextStr}" . PHP_EOL;

        file_put_contents($file, $logLine, FILE_APPEND | LOCK_EX);
    }
}
