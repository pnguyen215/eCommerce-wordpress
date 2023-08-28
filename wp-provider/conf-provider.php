<?php
require_once __DIR__ . './../conf.php';

function is_enabled_debug_mode(): bool
{
    if (empty(ENABLED_APP_DEBUG_MODE) || is_null(ENABLED_APP_DEBUG_MODE)) {
        return false;
    }
    return ENABLED_APP_DEBUG_MODE === "true" ? true : false;
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

function get_virtual_ldp_tracker_id(): int
{
    if (empty(VIRTUAL_SANDBOX_TRACKER_ID) || is_null(VIRTUAL_SANDBOX_TRACKER_ID)) {
        return 102;
    }
    return intval(VIRTUAL_SANDBOX_TRACKER_ID);
}

function is_enabled_using_woo_product_id(): bool
{
    if (empty(ENABLED_USING_WOO_PRODUCT_ID_REPLACED) || is_null(ENABLED_USING_WOO_PRODUCT_ID_REPLACED)) {
        return false;
    }
    return ENABLED_USING_WOO_PRODUCT_ID_REPLACED === "true" ? true : false;
}

function get_ldp_direction_inward(): string
{
    return "inward";
}

function get_ldp_direction_outward(): string
{
    return "outward";
}

function get_symbol_sku(): string
{
    return "-";
}

function get_redirect_link_order_completed(): string
{
    return empty(REDIRECT_LINK_ORDER_COMPLETED) && is_null(REDIRECT_LINK_ORDER_COMPLETED) ? "" : REDIRECT_LINK_ORDER_COMPLETED;
}

function get_redirect_link_order_failure(): string
{
    return empty(REDIRECT_LINK_ORDER_FAILURE) && is_null(REDIRECT_LINK_ORDER_FAILURE) ? "" : REDIRECT_LINK_ORDER_FAILURE;
}

function get_virtual_sandbox_province_id(): int
{
    if (empty(VIRTUAL_SANDBOX_PROVINCE_ID) || is_null(VIRTUAL_SANDBOX_PROVINCE_ID)) {
        return 0;
    }
    return intval(VIRTUAL_SANDBOX_PROVINCE_ID);
}

function get_virtual_sandbox_district_id(): int
{
    if (empty(VIRTUAL_SANDBOX_DISTRICT_ID) || is_null(VIRTUAL_SANDBOX_DISTRICT_ID)) {
        return 0;
    }
    return intval(VIRTUAL_SANDBOX_DISTRICT_ID);
}

function get_virtual_sandbox_ward_id(): int
{
    if (empty(VIRTUAL_SANDBOX_WARD_ID) || is_null(VIRTUAL_SANDBOX_WARD_ID)) {
        return 0;
    }
    return intval(VIRTUAL_SANDBOX_WARD_ID);
}


?>