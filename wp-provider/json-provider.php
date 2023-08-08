<?php

function to_json_console($value): void
{
    error_log(to_json_string($value));
}

function to_json_web($value): void
{
    echo to_json_string($value);
}

function to_json_console_pretty($value): void
{
    error_log("<code>" . to_json_string_pretty($value) . "</code>");
}

function to_json_web_pretty($value): void
{
    echo "<pre>" . to_json_string_pretty($value) . "</pre>";
}

function to_json_string($value): string|bool
{
    return json_encode($value, JSON_UNESCAPED_SLASHES);
}

function to_json_string_pretty($value): string|bool
{
    return json_encode($value, JSON_PRETTY_PRINT);
}

function message($type, $args): void
{
    $message = isset($args[0]) ? $args[0] : "[logger]";
    $value = isset($args[1]) ? $args[1] : null;

    $payload = array(
        'type' => $type,
        'message' => $message,
        'value' => $value
    );
    $json = to_json_string($payload);
    error_log($json);
}

function info(): void
{
    message('INFO', func_get_args());
}

function error(): void
{
    message('ERROR', func_get_args());
}

function warn(): void
{
    message('WARN', func_get_args());
}

function success(): void
{
    message('SUCCESS', func_get_args());
}

function debug(): void
{
    message("DEBUG", func_get_args());
}

function console_format($text, $color): string
{
    $colors = array(
        'black' => '0;30',
        'blue' => '0;34',
        'green' => '0;32',
        'cyan' => '0;36',
        'red' => '0;31',
        'purple' => '0;35',
        'brown' => '0;33',
        'yellow' => '1;33',
        'light_gray' => '0;37',
        'light_blue' => '1;34',
        'light_green' => '1;32',
        'light_cyan' => '1;36',
        'light_red' => '1;31',
        'light_purple' => '1;35',
        'white' => '1;37',
        'magenta' => '0;35'
    );

    if (array_key_exists($color, $colors)) {
        return "\033[" . $colors[$color] . "m" . $text . "\033[0m";
    } else {
        return $text;
    }
}

function json_decode_unescaped_slashes($jsonString): string
{
    $decoded = json_decode($jsonString, true, 512, JSON_UNESCAPED_SLASHES);
    return is_array($decoded) ? json_encode($decoded, JSON_UNESCAPED_SLASHES) : $jsonString;
}

function messageColor($type, $args, $color = null): void
{
    global $decodeUrls;
    $message = isset($args[0]) ? $args[0] : "[logger]";
    $value = isset($args[1]) ? $args[1] : null;

    $payload = array(
        'type' => $type,
        'message' => $message,
        'value' => $value
    );
    $json = to_json_string($payload);
    if ($decodeUrls) {
        $json = json_decode_unescaped_slashes($json);
    }
    error_log(console_format($json, $color));
}

function infoColor(...$args): void
{
    messageColor('INFO', $args, 'green');
}

function errorColor(...$args): void
{
    messageColor('ERROR', $args, 'red');
}

function warnColor(...$args): void
{
    messageColor('WARN', $args, 'yellow');
}

function successColor(...$args): void
{
    messageColor('SUCCESS', $args, 'cyan');
}

function debugColor(...$args): void
{
    messageColor("DEBUG", $args, 'magenta');
}

?>