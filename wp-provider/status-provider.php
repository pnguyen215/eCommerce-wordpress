<?php

function get_status_processing(): string
{
    return "processing";
}

function get_status_completed(): string
{
    return "completed";
}

function get_status_cancelled(): string
{
    return "cancelled";
}

function get_status_pending(): string
{
    return "pending-payment";
}

function get_status_awaiting(): string
{
    return "awaiting-payment";
}

function get_status_on_hold(): string
{
    return "on-hold";
}

function get_status_refunded(): string
{
    return "refunded";
}

function get_status_failed(): string
{
    return "failed";
}

function get_status_draft(): string
{
    return "draft";
}

function get_statuses(): array
{
    return array(
        get_status_processing() => true,
        get_status_completed() => true,
        get_status_cancelled() => true,
        get_status_pending() => true,
        get_status_awaiting() => true,
        get_status_on_hold() => true,
        get_status_refunded() => true,
        get_status_failed() => true,
        get_status_draft() => true,
    );
}

function is_woo_status($status): bool
{
    if (empty($status) || is_null($status)) {
        return false;
    }
    $statuses = get_statuses();
    return array_key_exists($status, $statuses) && $statuses[$status] === true;
}

?>