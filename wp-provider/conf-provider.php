<?php
require_once __DIR__ . './../conf.php';

function is_enabled_debug_mode(): bool
{
    if (empty(ENABLED_DEBUG_MODE) || is_null(ENABLED_DEBUG_MODE)) {
        return false;
    }
    return ENABLED_DEBUG_MODE === "true" ? true : false;
}

function is_enabled_generate_click_id(): bool
{
    if (empty(VIRTUAL_SANDBOX_ENABLED_GENERATE_CLICK_ID) || is_null(VIRTUAL_SANDBOX_ENABLED_GENERATE_CLICK_ID)) {
        return false;
    }
    return VIRTUAL_SANDBOX_ENABLED_GENERATE_CLICK_ID === "true" ? true : false;
}

function is_enabled_redirect_checkout_payment_url(): bool
{
    if (empty(ENABLED_REDIRECT_CHECKOUT_PAYMENT_URL) || is_null(ENABLED_REDIRECT_CHECKOUT_PAYMENT_URL)) {
        return false;
    }
    return ENABLED_REDIRECT_CHECKOUT_PAYMENT_URL === "true" ? true : false;
}

?>