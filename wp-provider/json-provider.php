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
    return json_encode($value);
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

?>