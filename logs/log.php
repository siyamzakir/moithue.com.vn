<?php
date_default_timezone_set('Asia/Dhaka');

final class Logger
{
    // Path to the log file
    private static $logFile = __DIR__ . '/app.log';

    /**
     * Write a log message with a specific level.
     *
     * @param string $level The log level (info, error, warning, debug, etc.)
     * @param string $message The log message
     * @param array $context Additional context data
     */
    public static function write(string $level, string $message, array $context = [])
    {
        // Format the log entry
        $timestamp = self::getLocalTime();
        $contextString = !empty($context) ? json_encode($context, JSON_PRETTY_PRINT) : '';
        $logEntry = "[$timestamp] $level: $message $contextString" . PHP_EOL;

        // Ensure the logs directory exists
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        // Write the log entry to the file
        file_put_contents(self::$logFile, $logEntry, FILE_APPEND);
    }

    /**
     * Log an informational message.
     *
     * @param string $message
     * @param array $context
     */
    public static function info(string $message, array $context = [])
    {
        self::write('INFO', $message, $context);
    }

    /**
     * Log an error message.
     *
     * @param string $message
     * @param array $context
     */
    public static function error(string $message, array $context = [])
    {
        self::write('ERROR', $message, $context);
    }

    
    /**
     * Log a message with a custom level.
     *
     * @param array $contexts spreads arguments
     */
    public function logger(...$contexts){
        self::write('LOGGER', json_encode($contexts, JSON_PRETTY_PRINT), []);
    }

    /**
     * Log a warning message.
     *
     * @param string $message
     * @param array $context
     */
    public static function warning(string $message, array $context = [])
    {
        self::write('WARNING', $message, $context);
    }

    /**
     * Log a debug message.
     *
     * @param string $message
     * @param array $context
     */
    public static function debug(string $message, array $context = [])
    {
        self::write('DEBUG', $message, $context);
    }

    /**
     * Get the formatted time for a given date and time string.
     *
     * @param string $dateTime The date and time string to be formatted.
     * @param string $format Optional. The format in which to return the date and time. Defaults to 'd/m/Y, h:i:s A'.
     * @return string The formatted date and time string.
     */

    public static function getLocalTime(string $dateTime = 'now', string $format = 'd/m/Y, h:i:s A') {
        $date = new DateTime($dateTime, new DateTimeZone('Asia/Dhaka'));
        return $date->format($format);
    }
}
 
