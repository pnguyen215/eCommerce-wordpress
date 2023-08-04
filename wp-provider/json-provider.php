<?php

function to_json_console($value): void
{
    error_log(to_json_string($value));
}

function to_json_console_pretty($value): void
{
    $pretty_json = json_encode($value, JSON_PRETTY_PRINT);
    error_log("<pre>" . $pretty_json . "</pre>");
}

function to_json_string($value): string
{
    return json_encode($value);
}

?>