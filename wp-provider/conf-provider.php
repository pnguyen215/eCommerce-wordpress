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

function is_enabled_redirect_notification(): bool
{
    if (empty(_2C2P_ENABLED_REDIRECT_NOTIFICATION_DEFAULT) || is_null(_2C2P_ENABLED_REDIRECT_NOTIFICATION_DEFAULT)) {
        return false;
    }
    return _2C2P_ENABLED_REDIRECT_NOTIFICATION_DEFAULT === "true" ? true : false;
}

function is_enabled_redirect_backend_url(): bool
{
    return !empty(_2C2P_REDIRECT_BACKEND_URL) && !is_null(_2C2P_REDIRECT_BACKEND_URL);
}

function is_enabled_redirect_frontend_url(): bool
{
    return !empty(_2C2P_REDIRECT_FRONTEND_URL) && !is_null(_2C2P_REDIRECT_FRONTEND_URL);
}

function get_app_timezone(): string
{
    if (empty(APP_TIMEZONE) || is_null(APP_TIMEZONE)) {
        return "Asia/Ho_Chi_Minh";
    }
    return APP_TIMEZONE;
}

function is_new_woo_version_by($version): bool
{
    if (is_null($version) || empty($version)) {
        $version = "2.1.0";
    }
    $woocommerce_version = get_woo_version_key();
    return version_compare($woocommerce_version, $version, '>=') ? true : false;
}

function get_woo_version_key(): string
{
    return get_option('woocommerce_version');
}

?>