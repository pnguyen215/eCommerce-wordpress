<?php
/**
 * Plugin Name: Order Landing Page Provider
 * Description: Custom plugin to handle orders from custom URL and create WooCommerce orders.
 * Version: 1.0.0
 * Author: Aris Nguyen
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Include the main plugin class
require_once plugin_dir_path(__FILE__) . 'provider/OrderLandingPageProvider.php';
require_once plugin_dir_path(__FILE__) . 'services/_2c2pService.php';

$_2c2pService = new _2C2PService();

// Initialize order landing page
function order_landing_page_plugin_init($_2c2pService)
{
    $order_plugin = new OrderLandingPageProvider($_2c2pService);
    $order_plugin->init();
}
add_action('plugins_loaded', function () use ($_2c2pService) {
    order_landing_page_plugin_init($_2c2pService);
});

// Listen fallback order landing page
function order_landing_page_plugin_listen($_2c2pService)
{
    $order_plugin = new OrderLandingPageProvider($_2c2pService);
    $order_plugin->listen();
}
add_action('plugins_loaded', function () use ($_2c2pService) {
    order_landing_page_plugin_listen($_2c2pService);
});

// Define a function to handle the "order-received" event 
function order_landing_page_received_event_handler($order_id, $_2c2pService)
{
    $order_plugin = new OrderLandingPageProvider($_2c2pService);
    $order_plugin->process_order_received_default($order_id);
}
add_action('woocommerce_thankyou', function ($order_id) use ($_2c2pService) {
    order_landing_page_received_event_handler($order_id, $_2c2pService);
}, 10, 1);